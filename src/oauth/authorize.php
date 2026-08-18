<?php
require_once __DIR__ . '/../inc/layout.php';
require_login();
$secure = lvl_secure();
$client = $_GET['client_id'] ?? 'voltboard';
$redir  = $_GET['redirect_uri'] ?? '/oauth/callback.php';
$state  = $_GET['state'] ?? '';
$allow  = '/oauth/callback.php';                       // the ONLY registered redirect URI
$external = (bool)preg_match('#^https?://#i', $redir);

if (isset($_GET['approve'])) {
    // SECURE: redirect_uri must exactly match the registered callback.
    if ($secure && strpos($redir, $allow) !== 0) {
        head('Authorize'); echo '<div class="panel"><h2>⛔ Blocked</h2><p style="color:var(--muted)">redirect_uri does not match a registered callback for this client.</p><a class="btn" href="/oauth/">← Back</a></div>'; foot(); exit;
    }
    $code = 'vlt_' . substr(md5($client . session_id()), 0, 20);
    // VULN (Low): redirect_uri is not validated → an attacker-controlled external URI steals the auth code.
    if (!$secure && $external) {
        head('Redirecting');
        echo '<div class="panel" style="max-width:640px"><h2 style="margin-top:0">↗️ Redirecting to '.e(parse_url($redir,PHP_URL_HOST)).'…</h2>
          <p style="color:var(--muted)">Authorization code <code>'.e($code).'</code> is being sent to <code>'.e($redir).'</code>.</p>
          <div style="background:rgba(52,211,153,.12);border:1px dashed var(--green);color:#a7f3d0;border-radius:10px;padding:.8rem;font-family:ui-monospace,monospace">
          🚩 Open redirect in <b>redirect_uri</b> — the auth code just leaked to an external site! Flag: VOLT{oauth_open_redirect}</div>
          <p style="margin-top:1rem"><a class="btn" href="/oauth/">← Back</a></p></div>';
        foot(); exit;
    }
    $sep = strpos($redir,'?')!==false ? '&' : '?';
    header('Location: ' . $redir . $sep . 'code=' . $code . ($state!=='' ? '&state='.$state : ''));
    exit;
}
head('Authorize');
$u = pf_user();
?>
<div class="panel" style="max-width:520px;margin:1rem auto">
  <div style="text-align:center"><div style="font-size:2.4rem">🔑</div>
    <h2 style="margin:.3rem 0">Authorize <?= e($client) ?></h2>
    <p style="color:var(--muted)"><b><?= e($client) ?></b> wants to access your VoltID profile (<?= e($u['email']) ?>).</p></div>
  <div style="background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:10px;padding:.8rem;margin:1rem 0;font-size:.82rem;color:var(--muted)">
    Redirect URI: <code><?= e($redir) ?></code><br>State: <code><?= $state!=='' ? e($state) : '(none)' ?></code></div>
  <a class="cta full" style="text-align:center;display:block" href="/oauth/authorize.php?client_id=<?= e(urlencode($client)) ?>&redirect_uri=<?= e(urlencode($redir)) ?>&state=<?= e(urlencode($state)) ?>&approve=1">✓ Authorize</a>
  <a class="btn full" style="margin-top:.5rem;text-align:center;display:block" href="/oauth/">Cancel</a>
</div>
<?php foot();
