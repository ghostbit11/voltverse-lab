<?php
require_once __DIR__ . '/../inc/core.php';
require_login();
header('Content-Type: application/json');
$secure = lvl_secure();
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$q  = trim($in['query'] ?? ($_POST['query'] ?? ''));
$ME = 1; // the API caller is user id 1 (customer)

function gql($d){ echo json_encode($d, JSON_PRETTY_PRINT); exit; }

if ($q === '') gql(['errors'=>[['message'=>'Empty query. Try: { me { id email } }']]]);

// --- Introspection (API: information disclosure) ---
if (preg_match('/__schema|__type/i', $q)) {
    if ($secure) gql(['errors'=>[['message'=>'Introspection has been disabled in production.']]]);
    gql(['data'=>['__schema'=>[
        'queryType'=>['name'=>'Query'],
        'types'=>[
            ['name'=>'Query','fields'=>['me','user(id)','products(filter)']],
            ['name'=>'User','fields'=>['id','email','name','password','secret','isAdmin']],
            ['name'=>'Product','fields'=>['id','name','price']],
        ],
        'note'=>'Introspection exposed the full schema — including sensitive User fields (password, secret).',
        'flag'=>'VOLT{graphql_introspection_exposed}',
    ]]]);
}
// --- user(id: N) → BOLA ---
if (preg_match('/user\s*\(\s*id\s*:\s*(\d+)\s*\)/i', $q, $m)) {
    $id = (int)$m[1];
    if ($secure && $id !== $ME) gql(['errors'=>[['message'=>'Forbidden — you may only query your own user.']]]);
    $st = db()->prepare("SELECT id,email,name,password,secret,is_admin FROM users WHERE id=?"); $st->execute([$id]);
    $u = $st->fetch(PDO::FETCH_ASSOC);
    if (!$u) gql(['data'=>['user'=>null]]);
    $r = ['id'=>$u['id'],'email'=>$u['email'],'name'=>$u['name'],'password'=>$u['password'],'secret'=>$u['secret'],'isAdmin'=>(bool)$u['is_admin']];
    if ($id !== $ME) { $r['note']='BOLA: you resolved another user object by id.'; $r['flag']='VOLT{graphql_bola}'; }
    gql(['data'=>['user'=>$r]]);
}
// --- products(filter: "...") → SQL injection through a resolver argument ---
if (preg_match('/products\s*\(\s*filter\s*:\s*"([^"]*)"\s*\)/i', $q, $m)) {
    $f = $m[1];
    if ($secure) {
        $st = db()->prepare("SELECT id,name,price FROM products WHERE name LIKE ?"); $st->execute(["%$f%"]);
        gql(['data'=>['products'=>$st->fetchAll(PDO::FETCH_ASSOC)]]);
    }
    try {
        $rows = db()->query("SELECT id,name,price FROM products WHERE name LIKE '%$f%'")->fetchAll(PDO::FETCH_ASSOC);
        $out = ['data'=>['products'=>$rows]];
        foreach ($rows as $rr) if (strpos(json_encode($rr),'VOLT{')!==false) { $out['flag']='VOLT{graphql_injection}'; break; }
        gql($out);
    } catch (Throwable $e) { gql(['errors'=>[['message'=>$e->getMessage()]]]); }
}
// --- me ---
if (preg_match('/\bme\b/i', $q)) {
    $st = db()->prepare("SELECT id,email,name FROM users WHERE id=?"); $st->execute([$ME]);
    gql(['data'=>['me'=>$st->fetch(PDO::FETCH_ASSOC)]]);
}
gql(['errors'=>[['message'=>'Unknown field. Try { me { id email } }, user(id:2), products(filter:"book") or __schema.']]]);
