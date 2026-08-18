<?php
// VoltVerse platform core: DB, sessions, difficulty level, helpers.
session_start();

function db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    $path = __DIR__ . '/../data/vv.db';
    $fresh = !file_exists($path);
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($fresh) seed($pdo);
    return $pdo;
}

function seed(PDO $pdo): void {
    $pdo->exec("
    CREATE TABLE platform_users (id INTEGER PRIMARY KEY, email TEXT UNIQUE, pass TEXT, name TEXT);
    -- STORE app
    CREATE TABLE users (id INTEGER PRIMARY KEY, email TEXT UNIQUE, password TEXT, name TEXT, is_admin INTEGER DEFAULT 0, secret TEXT);
    CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, category TEXT, price REAL, emoji TEXT, descr TEXT, rating REAL);
    CREATE TABLE reviews (id INTEGER PRIMARY KEY, product_id INTEGER, author TEXT, body TEXT);
    CREATE TABLE orders (id INTEGER PRIMARY KEY, user_email TEXT, total REAL, status TEXT);
    CREATE TABLE order_items (id INTEGER PRIMARY KEY, order_id INTEGER, name TEXT, qty INTEGER, price REAL);
    -- BANK app
    CREATE TABLE accounts (id INTEGER PRIMARY KEY, owner TEXT, holder TEXT, number TEXT, balance REAL, secret TEXT);
    CREATE TABLE txns (id INTEGER PRIMARY KEY, account_id INTEGER, descr TEXT, amount REAL, ts TEXT);
    ");
    // store data
    $pdo->exec("INSERT INTO users(email,password,name,is_admin,secret) VALUES
      ('customer@volt.local','password123','Alex Customer',0,NULL),
      ('admin@volt.local','S3cretAdmin!','Site Admin',1,'VOLT{store_sqli_admin_secret}');");
    $prods = [
      ['VoltBook Pro 14','Laptops',1299,'💻','M3 chip, 18h battery, 512GB SSD',4.7],
      ['VoltPhone 15','Phones',899,'📱','6.1\" OLED, titanium, triple camera',4.6],
      ['VoltBuds Wireless','Audio',149,'🎧','ANC earbuds, 30h case',4.4],
      ['VoltWatch 7','Wearables',399,'⌚','Always-on, ECG, GPS',4.5],
      ['VoltPad Air','Tablets',649,'📲','11\" display, VoltPencil support',4.3],
      ['Volt Mechanical KB','Accessories',129,'⌨️','Hot-swap, RGB, USB-C',4.8],
      ['VoltMouse Pro','Accessories',79,'🖱️','8K sensor, 70h battery',4.2],
      ['Volt 4K Monitor 27"','Displays',549,'🖥️','4K 144Hz, USB-C hub',4.5],
    ];
    $s = $pdo->prepare("INSERT INTO products(name,category,price,emoji,descr,rating) VALUES(?,?,?,?,?,?)");
    foreach ($prods as $p) $s->execute($p);
    $pdo->exec("INSERT INTO reviews(product_id,author,body) VALUES (1,'Priya','Blazing fast!'),(2,'Sam','Best yet.')");
    $pdo->exec("INSERT INTO orders(id,user_email,total,status) VALUES
      (1001,'customer@volt.local',1448,'delivered'),
      (1002,'admin@volt.local',899,'confirmed — VOLT{store_idor_other_order}')");
    $pdo->exec("INSERT INTO order_items(order_id,name,qty,price) VALUES
      (1001,'VoltBook Pro 14',1,1299),(1001,'VoltBuds Wireless',1,149),(1002,'VoltPhone 15',1,899)");
    // bank data
    $pdo->exec("INSERT INTO accounts(id,owner,holder,number,balance,secret) VALUES
      (5001,'customer@volt.local','Alex Customer','VV-5001',4200.00,NULL),
      (5002,'admin@volt.local','VoltCorp Treasury','VV-5002',999999.00,'VOLT{bank_idor_treasury}')");
    $pdo->exec("INSERT INTO txns(account_id,descr,amount,ts) VALUES
      (5001,'Salary',5000,'2026-08-01'),(5001,'Grocery',-320,'2026-08-03'),(5001,'ATM',-480,'2026-08-05')");
}

// ---- difficulty level (bWAPP style) ---------------------------------------
function level(): string {
    $l = $_COOKIE['vv_level'] ?? 'low';
    return in_array($l, ['low','medium','high','secure']) ? $l : 'low';
}
function lvl_secure(): bool { return level() === 'secure'; }

// ---- platform auth (real gate) --------------------------------------------
function pf_user(): ?array { return $_SESSION['pf'] ?? null; }

/* ---- instructor-managed settings (key/value) ---- */
function ensure_settings(): void { db()->exec("CREATE TABLE IF NOT EXISTS settings(k TEXT PRIMARY KEY, v TEXT)"); }
function setting(string $k, string $default=''): string {
    ensure_settings(); $st=db()->prepare("SELECT v FROM settings WHERE k=?"); $st->execute([$k]);
    $r=$st->fetchColumn(); return $r===false ? $default : (string)$r;
}
function set_setting(string $k, string $v): void {
    ensure_settings(); db()->prepare("INSERT OR REPLACE INTO settings(k,v) VALUES(?,?)")->execute([$k,$v]);
}

function is_instructor(): bool {
    $me = pf_user(); if (!$me) return false;
    $first = db()->query("SELECT email FROM platform_users ORDER BY id LIMIT 1")->fetchColumn();
    return $first !== false && $first === $me['email'];
}
function require_login(): void {
    if (!pf_user()) { header('Location: /login.php'); exit; }
    siem_scan();
}

// ---- Blue-team SIEM: log suspicious requests automatically --------------
function ensure_siem(): void {
    db()->exec("CREATE TABLE IF NOT EXISTS siem(id INTEGER PRIMARY KEY AUTOINCREMENT, ts TEXT DEFAULT (datetime('now')), ip TEXT, actor TEXT, type TEXT, sev TEXT, uri TEXT, detail TEXT)");
}
function log_event(string $type, string $sev, string $detail): void {
    ensure_siem();
    db()->prepare("INSERT INTO siem(ip,actor,type,sev,uri,detail) VALUES(?,?,?,?,?,?)")
        ->execute([$_SERVER['REMOTE_ADDR'] ?? '', (pf_user()['email'] ?? 'anon'), $type, $sev, substr($_SERVER['REQUEST_URI'] ?? '',0,180), $detail]);
}
function siem_scan(): void {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($uri, '/soc.php') !== false) return;   // don't log the SOC page itself
    $blob = strtolower($uri . ' ' . implode(' ', array_map(fn($v)=>is_string($v)?$v:'', $_REQUEST)));
    $sigs = [
      'SQLi'          => ['union select','or 1=1',"' or ",'information_schema',"'--",'" or "'],
      'XSS'           => ['<script','onerror=','onload=','javascript:'],
      'Command Inj'   => ['; cat','; ls','&&','| cat','$(', '`'],
      'Path Traversal'=> ['../','..%2f','/etc/passwd','secret_lfi','corp_secret'],
      'SSRF'          => ['127.0.0.1','169.254.','internal.php'],
      'SSTI'          => ['{{7','{{ 7','{{system','{{config'],
      'Prompt Inj'    => ['ignore previous','ignore all previous','system prompt','do anything now'],
    ];
    foreach ($sigs as $type=>$pats) foreach ($pats as $p)
        if (strpos($blob, $p) !== false) { log_event($type, 'high', 'signature "'.$p.'"'); break; }
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
