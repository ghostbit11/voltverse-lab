<?php
require_once __DIR__ . '/inc/layout.php';
require_login();                      // exempt from the mustpw redirect (handled in require_login)
$u = pf_user();
$forced = must_change_password();
$err = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (strlen($new) < 4) $err = 'Choose a password of at least 4 characters.';
    elseif ($new !== $confirm) $err = 'The two passwords do not match.';
    else {
        set_user_password($u['email'], $new);
        set_setting('mustpw:'.$u['email'], '0');   // clear the first-login flag
        header('Location: /dashboard.php'); exit;
    }
}
head('Set your password');
?>
<div class="auth fadeup">
  <div class="auth-hero">
    <div class="ring"><?= logomark(38) ?></div>
    <div style="justify-content:center;display:flex"><?= brand('1.7rem') ?></div>
    <p style="color:var(--muted);margin:.5rem 0 0"><?= $forced ? 'Welcome! Set your own password to continue' : 'Change your password' ?></p>
  </div>
  <div class="panel">
    <?php if ($forced): ?><div style="background:var(--accent-soft);border:1px solid var(--accent-line);color:#9db8ff;border-radius:9px;padding:.6rem .9rem;margin-bottom:.8rem;font-size:.84rem">Your account was created by an admin with a temporary password. Please choose your own password now.</div><?php endif; ?>
    <?php if ($err): ?><div class="note"><?= e($err) ?></div><?php endif; ?>
    <form method="post">
      <label>New password</label><input type="password" name="password" required autofocus>
      <label>Confirm new password</label><input type="password" name="confirm" required>
      <div style="margin-top:1.2rem"><button class="btn full" type="submit">Save password &amp; continue →</button></div>
    </form>
  </div>
</div>
<?php foot();
