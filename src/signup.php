<?php
require_once __DIR__ . '/inc/layout.php';
$err = null;
$noAdmin = !admin_exists();                          // no admin yet → this person can set up as admin
$closed  = admin_exists() && setting('open_signup','1') !== '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$closed) {
    $email = trim($_POST['email'] ?? ''); $name = trim($_POST['name'] ?? ''); $pass = $_POST['password'] ?? '';
    $wantAdmin = ($_POST['acctype'] ?? '') === 'admin';
    // Admin is granted only when you ask for it AND no admin exists yet (workspace setup).
    $role = ($wantAdmin && $noAdmin) ? 'admin' : 'member';
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
      <?php if ($noAdmin): ?>
      <label>Account type</label>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-bottom:.3rem">
        <label class="acctopt" style="display:block;text-transform:none;letter-spacing:0;margin:0;cursor:pointer;border:1px solid var(--accent-line);background:var(--accent-soft);border-radius:10px;padding:.7rem .8rem">
          <input type="radio" name="acctype" value="admin" checked style="width:auto;margin-right:.4rem">👑 <b>Admin</b><br><span style="color:var(--muted);font-size:.78rem">Set up a workspace &amp; manage your team</span></label>
        <label class="acctopt" style="display:block;text-transform:none;letter-spacing:0;margin:0;cursor:pointer;border:1px solid var(--line);border-radius:10px;padding:.7rem .8rem">
          <input type="radio" name="acctype" value="member" style="width:auto;margin-right:.4rem">👤 <b>Member</b><br><span style="color:var(--muted);font-size:.78rem">Just here to learn &amp; solve</span></label>
      </div>
      <?php else: ?>
      <div style="background:var(--accent-soft);border:1px solid var(--accent-line);color:#9db8ff;border-radius:9px;padding:.6rem .9rem;margin-bottom:.8rem;font-size:.84rem">This workspace is managed by an admin — you'll join as a <b>member</b>. (Your admin can change your role later.)</div>
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
