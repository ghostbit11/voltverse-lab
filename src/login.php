<?php
require_once __DIR__ . '/inc/layout.php';
$err = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); $pass = $_POST['password'] ?? '';
    $st = db()->prepare("SELECT * FROM platform_users WHERE email=?"); $st->execute([$email]);
    $u = $st->fetch(PDO::FETCH_ASSOC);
    if ($u && password_verify($pass, $u['pass'])) { $_SESSION['pf'] = ['email'=>$u['email'],'name'=>$u['name']]; header('Location: /dashboard.php'); exit; }
    $err = 'Invalid email or password.';
}
head('Sign in');
?>
<div class="auth fadeup">
  <div class="auth-hero">
    <div class="ring"><?= logomark(38) ?></div>
    <div style="justify-content:center;display:flex"><?= brand('1.7rem') ?></div>
    <p style="color:var(--muted);margin:.5rem 0 0">Sign in to your cybersecurity range</p>
  </div>
  <div class="panel">
    <?php if ($err): ?><div class="note"><?= e($err) ?></div><?php endif; ?>
    <form method="post">
      <label>Email</label><input type="email" name="email" placeholder="you@example.com" required>
      <label>Password</label><input type="password" name="password" required>
      <div style="margin-top:1.2rem"><button class="btn full" type="submit">Sign in →</button></div>
    </form>
    <p style="text-align:center;color:var(--muted);margin-top:1rem">New here? <a href="/signup.php">Create an account</a></p>
  </div>
</div>
<?php foot();
