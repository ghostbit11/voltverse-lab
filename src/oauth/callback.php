<?php
require_once __DIR__ . '/../inc/layout.php';
require_login();
$secure = lvl_secure();
$code  = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';
$flag = null; $err = null;
// SECURE: the callback must validate `state` against the value it issued (CSRF defence).
if ($secure) {
    if ($state === '' || !hash_equals($_SESSION['oauth_state'] ?? '', $state)) $err = 'Invalid or missing state — request rejected (CSRF protection).';
} else {
    // VULN (Low): state is never verified → a forged callback with no state is accepted.
    if ($state === '') $flag = 'VOLT{oauth_missing_state}';
}
head('OAuth callback');
?>
<div class="panel" style="max-width:560px;margin:1rem auto">
  <?php if ($err): ?>
    <div style="text-align:center"><div style="font-size:2.4rem">⛔</div><h2><?= e($err) ?></h2></div>
  <?php else: ?>
    <div style="text-align:center"><div style="font-size:2.4rem">✅</div><h2 style="margin:.3rem 0">Signed in with VoltID</h2>
      <p style="color:var(--muted)">Authorization code: <code><?= e($code ?: '(none)') ?></code></p></div>
    <?php if ($flag): ?>
      <div style="background:rgba(52,211,153,.12);border:1px dashed var(--green);color:#a7f3d0;border-radius:10px;padding:.8rem;margin-top:1rem;font-family:ui-monospace,monospace">
        🚩 The callback accepted a request with <b>no state parameter</b> — login CSRF! Flag: VOLT{oauth_missing_state}</div>
    <?php endif; ?>
  <?php endif; ?>
  <p style="margin-top:1rem;text-align:center"><a class="btn" href="/oauth/">← Back to VoltConnect</a></p>
</div>
<?php foot();
