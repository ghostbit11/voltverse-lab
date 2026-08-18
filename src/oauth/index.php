<?php
require_once __DIR__ . '/../inc/layout.php';
require_login();
// the "client app" starts an OAuth flow — it generates a state and remembers it
$state = substr(md5(session_id().'oauth'), 0, 12);
$_SESSION['oauth_state'] = $state;
$APP = ['ico'=>'🔑','name'=>'VoltConnect (OAuth SSO)'];
head('VoltConnect', $APP);
?>
<div style="margin-bottom:1rem"><a href="/dashboard.php">← Apps</a></div>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">🔑 VoltConnect · Single Sign-On</span>
  <h1 style="font-size:2rem">Sign in with VoltID</h1>
  <p>Third-party apps use VoltConnect (OAuth 2.0) to let users log in with their VoltID account.</p>
</div>
<div class="panel" style="margin-top:1.4rem;max-width:640px">
  <h3 style="margin-top:0">Demo relying-party</h3>
  <p style="color:var(--muted)">"VoltBoard" wants to authenticate you via VoltID. This kicks off the authorize step:</p>
  <a class="btn full" href="/oauth/authorize.php?client_id=voltboard&redirect_uri=/oauth/callback.php&state=<?= e($state) ?>">Continue with VoltID →</a>
  <div class="note" style="margin-top:1rem;color:var(--muted);font-size:.82rem">The authorize endpoint takes <code>client_id</code>, <code>redirect_uri</code> and <code>state</code> parameters.</div>
</div>
<?php foot();
