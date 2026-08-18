<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$stu = $_COOKIE['st_user'] ?? 'customer@volt.local';
$cur = db()->prepare("SELECT * FROM users WHERE email=?"); $cur->execute([$stu]); $cur=$cur->fetch(PDO::FETCH_ASSOC);
$secure = lvl_secure();

// A01 Broken Access Control: at Low/Med/High anyone can open /store/admin.php
if ($secure && empty($cur['is_admin'])) {
    store_head('Admin'); echo '<div class="section"><h2>403 — administrators only.</h2><a href="/store/">← Store</a></div>'; store_foot(); exit;
}

// A03 Command Injection: "server tools" ping
$ping = null;
if (isset($_POST['host'])) {
    $host = $_POST['host'];
    if ($secure) { $host = escapeshellarg(preg_replace('/[^a-z0-9.\-]/i','',$host)); $ping = shell_exec("ping -c 1 $host 2>&1"); }
    else { $ping = shell_exec("ping -c 1 $host 2>&1"); }   // VULN: raw input to shell
}
// A10 SSRF: import product image from URL
$ssrf = null;
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    if ($secure && !preg_match('#^https?://(cdn\.voltverse\.local)/#',$url)) $ssrf = "Blocked: only cdn.voltverse.local is allowed.";
    else { $ssrf = @file_get_contents($url); if ($ssrf===false) $ssrf = "Could not fetch."; }  // VULN: fetch any URL
}
store_head('Admin console');
$users = db()->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="crumb"><a href="/store/">Home</a> › Admin</div>
<div class="notice">🛠 <b>Admin console</b> — staff only.<?php if (!$secure && empty($cur['is_admin'])): ?>
  <b> You are not an admin, yet this page loaded — broken access control (A01). Flag: VOLT{store_broken_access_admin}</b><?php endif; ?></div>

<div class="section"><h2 style="margin-top:0">👥 Users (A02 · sensitive data exposure)</h2>
  <table style="width:100%"><tr style="text-align:left;color:#878787;font-size:.8rem"><th style="padding:.5rem">Email</th><th>Password (plaintext!)</th><th>Admin</th><th>Secret</th></tr>
  <?php foreach ($users as $u): ?>
    <tr style="border-top:1px solid #f0f0f0"><td style="padding:.6rem .5rem"><?= e($u['email']) ?></td>
      <td style="font-family:monospace;color:#c0392b"><?= e($u['password']) ?></td><td><?= $u['is_admin']?'✔':'' ?></td>
      <td style="font-family:monospace;color:#388e3c"><?= e($u['secret']) ?></td></tr>
  <?php endforeach; ?></table>
</div>

<div class="two">
  <div class="section"><h2 style="margin-top:0">🌐 Server tools — ping (A03 · command injection)</h2>
    <form method="post"><div class="field"><label>Host to ping</label><input name="host" value="127.0.0.1"></div>
      <button class="btn btn-blue">Run ping</button></form>
    <?php if ($ping!==null): ?><pre style="background:#0b1020;color:#7CFC00;padding:.8rem;border-radius:6px;overflow:auto;font-size:.8rem"><?= e($ping) ?></pre><?php endif; ?>
    <p style="color:#878787;font-size:.82rem">Diagnostics · runs a reachability check against the given host.</p>
  </div>

  <div class="section"><h2 style="margin-top:0">🖼 Import product image (A10 · SSRF)</h2>
    <form method="post"><div class="field"><label>Image URL</label><input name="url" value="http://cdn.voltverse.local/promo.png"></div>
      <button class="btn btn-blue">Fetch</button></form>
    <?php if ($ssrf!==null): ?><pre style="background:#0b1020;color:#93c5fd;padding:.8rem;border-radius:6px;overflow:auto;font-size:.8rem"><?= e(substr($ssrf,0,600)) ?></pre><?php endif; ?>
    <p style="color:#878787;font-size:.82rem">Imports a product image from a URL onto our CDN.</p>
  </div>
</div>
<?php store_foot();
