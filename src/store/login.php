<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$msg = null; $secure = lvl_secure();
if (isset($_GET['as'])) { setcookie('st_user','customer@volt.local',0,'/'); header('Location: /store/account.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email=$_POST['email']??''; $pass=$_POST['password']??''; $row=null;
    if ($secure) { $st=db()->prepare("SELECT * FROM users WHERE email=? AND password=?"); $st->execute([$email,$pass]); $row=$st->fetch(PDO::FETCH_ASSOC); }
    else {
        $lvl = level(); $inj = $email;
        // MEDIUM: naive blacklist strips the keywords once → nest them (OORR) so a copy survives.
        if ($lvl === 'medium')     $inj = str_ireplace(['or','union','select'], '', $email);
        // HIGH: strips space-delimited keywords → wrap OR in /**/ comments to bypass.
        elseif ($lvl === 'high')   $inj = preg_replace('/\s+(or|union|select)\s+/i', ' ', $email);
        try { $row=db()->query("SELECT * FROM users WHERE email='$inj' AND password='$pass'")->fetch(PDO::FETCH_ASSOC); } catch(Throwable $e){ $row=null; }
    }
    if ($row){ setcookie('st_user',$row['email'],0,'/'); header('Location: /store/account.php'); exit; }
    $msg='Invalid email or password.';
}
store_head('Sign in');
?>
<div class="crumb"><a href="/store/">Home</a> › Sign in</div>
<div class="section" style="max-width:420px;margin:1rem auto">
  <h2 style="margin-top:0">Customer sign in</h2>
  <?php if ($msg): ?><div class="notice"><?= e($msg) ?></div><?php endif; ?>
  <form method="post">
    <div class="field"><label>Email</label><input name="email" placeholder="you@volt.local"></div>
    <div class="field"><label>Password</label><input type="password" name="password"></div>
    <button class="btn btn-buy full" type="submit">Sign in</button>
  </form>
  <p style="color:#878787;font-size:.85rem;margin-top:1rem">Demo: customer@volt.local / password123</p>
</div>
<?php store_foot();
