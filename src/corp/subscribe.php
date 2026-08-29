<?php
require_once __DIR__ . '/../inc/site_layout.php';
$cfg = ['brand'=>'VoltCorp','ico'=>'🌐','accent'=>'#0d9488','lab'=>'VoltCorp Website','home'=>'/corp/','nav'=>[['Home','/corp/']],'cta'=>'Home','cta_href'=>'/corp/'];
$secure = lvl_secure();
$email = $_REQUEST['email'] ?? '';
$token = $_REQUEST['csrf'] ?? '';
$ok = false; $csrf_flag = false;

if ($secure) {
    // SECURE: require POST + a valid anti-CSRF token
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        site_head('Subscription', $cfg);
        echo '<div class="card" style="margin-top:2rem"><h2>Request blocked</h2>
        <p style="color:#64748b">This change requires a valid CSRF token and a POST request.</p><a href="/corp/">← Back</a></div>';
        site_foot('VoltCorp'); exit;
    }
    if ($email) { setcookie('corp_sub', $email, 0, '/'); $ok = true; }
} else {
    // VULN: no token, accepts GET → CSRF
    if ($email) { setcookie('corp_sub', $email, 0, '/'); $ok = true;
        if ($_SERVER['REQUEST_METHOD'] === 'GET') $csrf_flag = true; }
}
site_head('Subscription updated', $cfg);
?>
<div class="card" style="margin-top:2rem;text-align:center;max-width:560px;margin-left:auto;margin-right:auto">
  <div style="font-size:2.4rem"><?= $ok?'✅':'⚠️' ?></div>
  <h2><?= $ok ? 'Notification email updated' : 'No email provided' ?></h2>
  <?php if ($ok): ?><p style="color:#64748b">Notifications will now be sent to <b><?= e($email) ?></b>.</p><?php endif; ?>
  <?php if ($csrf_flag): ?><div class="flag">🚩 That state change happened via a GET request with no CSRF token — CSRF! Flag: VOLT{corp_csrf_no_token}</div><?php endif; ?>
  <a href="/corp/">← Back to VoltCorp</a>
</div>
<?php site_foot('VoltCorp');
