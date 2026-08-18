<?php
require_once __DIR__ . '/../inc/core.php';
require_login();
header('Content-Type: application/json');
$secure = lvl_secure();

$path = trim($_SERVER['PATH_INFO'] ?? ($_GET['r'] ?? ''), '/');
$seg  = $path === '' ? [] : explode('/', $path);
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) $body = $_POST;
$apikey = $_SERVER['HTTP_X_API_KEY'] ?? ($_GET['api_key'] ?? '');
$ME = 1;   // the API caller is customer (user id 1)

function out($d, $code = 200) { http_response_code($code); echo json_encode($d, JSON_PRETTY_PRINT); exit; }
function user_row($id) { $s = db()->prepare("SELECT id,email,name,is_admin,password,secret FROM users WHERE id=?"); $s->execute([$id]); return $s->fetch(PDO::FETCH_ASSOC); }

// API2 · Broken authentication — endpoints "require" a key but Low accepts any/missing
if ($secure && $apikey !== 'vlt_live_9f3c...REDACTED') {
    out(['error' => 'Unauthorized — valid X-API-Key required'], 401);
}
$brokenAuth = (!$secure && $apikey === '');

if (empty($seg)) out(['api'=>'Voltmart API v1','endpoints'=>['GET /users','GET /users/{id}','POST /users','DELETE /users/{id}','GET /orders/{id}']]);

// ---- /users ----
if ($seg[0] === 'users') {
    // GET /users  → API3 Excessive Data Exposure (returns password hashes/plaintext for everyone)
    if (count($seg) === 1 && $method === 'GET') {
        if ($secure) { $u = user_row($ME); out(['users'=>[['id'=>$u['id'],'email'=>$u['email'],'name'=>$u['name']]]]); }
        $rows = db()->query("SELECT id,email,name,is_admin,password FROM users")->fetchAll(PDO::FETCH_ASSOC);
        $r = ['users'=>$rows, 'note'=>'API3: this response leaks password fields for ALL users.',
              'flag'=>'VOLT{api_excessive_data_exposure}'];
        if ($brokenAuth) $r['auth_flag'] = 'VOLT{api_broken_auth}';
        out($r);
    }
    // GET /users/{id} → API1 BOLA
    if (count($seg) === 2 && $method === 'GET') {
        $id = (int)$seg[1]; $u = user_row($id);
        if (!$u) out(['error'=>'not found'], 404);
        if ($secure && $id !== $ME) out(['error'=>'Forbidden — you can only access your own user'], 403);
        $resp = ['id'=>$u['id'],'email'=>$u['email'],'name'=>$u['name'],'is_admin'=>(bool)$u['is_admin'],'password'=>$u['password'],'secret'=>$u['secret']];
        if ($id !== $ME) { $resp['note']='API1 BOLA: you read another user by changing the id.'; $resp['flag']='VOLT{api_bola_user_data}'; }
        out($resp);
    }
    // POST /users → API6 Mass Assignment (is_admin settable from body)
    if (count($seg) === 1 && $method === 'POST') {
        $email = $body['email'] ?? 'new@volt.local'; $name = $body['name'] ?? 'New User';
        $isAdmin = $secure ? 0 : (int)($body['is_admin'] ?? 0);    // VULN: trusts client is_admin
        db()->prepare("INSERT OR IGNORE INTO users(email,password,name,is_admin) VALUES(?,?,?,?)")->execute([$email,'changeme',$name,$isAdmin]);
        $resp = ['created'=>true,'email'=>$email,'is_admin'=>(bool)$isAdmin];
        if (!$secure && $isAdmin) { $resp['note']='API6 Mass assignment: you set is_admin via the request body.'; $resp['flag']='VOLT{api_mass_assignment_admin}'; }
        out($resp, 201);
    }
    // DELETE /users/{id} → API5 BFLA (admin-only function, no role check)
    if (count($seg) === 2 && $method === 'DELETE') {
        if ($secure) out(['error'=>'Forbidden — admin role required'], 403);
        out(['deleted'=>(int)$seg[1],'note'=>'API5 BFLA: a normal user invoked an admin-only function.','flag'=>'VOLT{api_bfla_admin_function}']);
    }
}
// ---- /orders/{id} → BOLA on orders ----
if ($seg[0] === 'orders' && count($seg) === 2 && $method === 'GET') {
    $id = (int)$seg[1]; $o = db()->prepare("SELECT * FROM orders WHERE id=?"); $o->execute([$id]); $o=$o->fetch(PDO::FETCH_ASSOC);
    if (!$o) out(['error'=>'not found'],404);
    if ($secure && $o['user_email']!=='customer@volt.local') out(['error'=>'Forbidden'],403);
    out(['order'=>$o]);
}
out(['error'=>'Unknown endpoint: '.$method.' /'.$path], 404);
