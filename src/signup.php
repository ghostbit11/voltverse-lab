<?php
require_once __DIR__ . '/inc/layout.php';
$err = null;
$firstUser = !has_any_user();                        // very first account = super administrator (lab owner)
$closed    = !$firstUser && setting('open_signup','1') !== '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$closed) {
    $email = trim($_POST['email'] ?? ''); $name = trim($_POST['name'] ?? ''); $pass = $_POST['password'] ?? '';
    $role = $firstUser ? 'superadmin' : 'member';
    if (!$email || !$pass) $err = 'Email and password required.';
    else { try {
        create_platform_user($email, $name, $pass, $role);
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
    <?php if ($closed): ?><div class="note">Registration is closed. Ask your workspace admin to create an account for you.</div><?php endif; ?>
    <?php if ($err): ?><div class="note"><?= e($err) ?></div><?php endif; ?>
    <form method="post" <?= $closed?'style="opacity:.5;pointer-events:none"':'' ?>>
      <?php if ($firstUser): ?>
      <div style="background:var(--accent-soft);border:1px solid var(--accent-line);color:#9db8ff;border-radius:9px;padding:.6rem .9rem;margin-bottom:.8rem;font-size:.84rem">👑 You're setting up VoltVerse as the <b>super administrator</b> — full control of the lab and every user.</div>
      <?php else: ?>
      <div style="background:var(--accent-soft);border:1px solid var(--accent-line);color:#9db8ff;border-radius:9px;padding:.6rem .9rem;margin-bottom:.8rem;font-size:.84rem">You'll join as a <b>member</b>. Your admin manages your access and assigns your tests.</div>
      <?php endif; ?>
      <label>Full name</label><input type="text" name="name" placeholder="Your name">
      <label>Email</label><input type="email" name="email" placeholder="you@example.com" required>
      <label>Password</label><input type="password" name="password" required>
      <div style="margin-top:1.2rem"><button class="btn full" type="submit">Create account →</button></div>
    </form>
    <p style="text-align:center;color:var(--muted);margin-top:1rem">Already have an account? <a href="/login.php">Sign in</a></p>
  </div>
</div>
<?php foot();
