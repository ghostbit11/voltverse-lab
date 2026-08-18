<?php
require_once __DIR__ . '/inc/layout.php';
$err = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); $name = trim($_POST['name'] ?? ''); $pass = $_POST['password'] ?? '';
    if (!$email || !$pass) $err = 'Email and password required.';
    else { try {
        db()->prepare("INSERT INTO platform_users(email,pass,name) VALUES(?,?,?)")->execute([$email, password_hash($pass, PASSWORD_DEFAULT), $name ?: $email]);
        $_SESSION['pf'] = ['email'=>$email,'name'=>$name ?: $email]; header('Location: /dashboard.php'); exit;
    } catch (Throwable $e) { $err = 'That email is already registered.'; } }
}
head('Create account');
?>
<div class="auth fadeup">
  <div class="auth-hero">
    <div class="ring"><?= logomark(38) ?></div>
    <div style="justify-content:center;display:flex"><?= brand('1.7rem') ?></div>
    <p style="color:var(--muted);margin:.5rem 0 0">Create your hacking range account</p>
  </div>
  <div class="panel">
    <?php if ($err): ?><div class="note"><?= e($err) ?></div><?php endif; ?>
    <form method="post">
      <label>Full name</label><input type="text" name="name" placeholder="Your name">
      <label>Email</label><input type="email" name="email" placeholder="you@example.com" required>
      <label>Password</label><input type="password" name="password" required>
      <div style="margin-top:1.2rem"><button class="btn full" type="submit">Create account →</button></div>
    </form>
    <p style="text-align:center;color:var(--muted);margin-top:1rem">Already have an account? <a href="/login.php">Sign in</a></p>
  </div>
</div>
<?php foot();
