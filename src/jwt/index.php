<?php
require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/jwt.php';
require_login();
$APP = ['ico'=>'🔐','name'=>'VoltID (JWT SSO)'];
$secure = lvl_secure();
$mytoken = jwt_make(['sub'=>'customer@volt.local','role'=>'user','iat'=>1735689600]);
$result = null; $flag = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tok = trim($_POST['token'] ?? '');
    $pl = jwt_verify($tok, $secure);
    if (!$pl) $result = ['bad','Token rejected — invalid signature or algorithm.'];
    elseif (($pl['role'] ?? '') !== 'admin') $result = ['ok','Token valid — but your role is "'.($pl['role']??'?').'". Admin required.'];
    else {
        [$hd] = jwt_parse($tok); $alg = strtolower($hd['alg'] ?? '');
        if ($alg === 'none') $flag = 'VOLT{jwt_alg_none_bypass}';
        else $flag = 'VOLT{jwt_weak_secret}';
        $result = ['admin','Welcome, admin! Full access granted.'];
    }
}
head('VoltID', $APP);
?>
<div style="margin-bottom:1rem"><a href="/dashboard.php">← Apps</a></div>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">🔐 VoltID · Single Sign-On</span>
  <h1 style="font-size:2rem">JWT token service</h1>
  <p>VoltID issues signed JWTs for the VoltVerse apps. Your session token is below — can you become an <b>admin</b>?</p>
</div>
<div class="aiwrap" style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;margin-top:1.4rem">
  <div class="panel">
    <h3 style="margin-top:0">Your token (role: user)</h3>
    <textarea id="mt" rows="4" readonly style="width:100%;background:rgba(255,255,255,.05);border:1px solid var(--line);color:#93c5fd;border-radius:10px;padding:.7rem;font-family:ui-monospace,monospace;font-size:.78rem;word-break:break-all"><?= e($mytoken) ?></textarea>
    <button class="btn" style="margin-top:.5rem" onclick="forgeNone()">⚡ Auto-forge alg:none (role=admin)</button>
    <div class="note" style="margin-top:.8rem;color:var(--muted)">
      💡 Two ways in at Low difficulty:<br>1) Set header <code>{"alg":"none"}</code> and empty signature.<br>
      2) The HMAC secret is weak — crack it (it's <code>secret</code>) and re-sign with <code>role:admin</code> on jwt.io.</div>
  </div>
  <div class="panel">
    <h3 style="margin-top:0">Access admin panel</h3>
    <form method="post"><label style="font-size:.76rem;color:var(--muted)">Paste a JWT</label>
      <textarea name="token" id="tin" rows="4" placeholder="eyJ..." style="width:100%;background:rgba(255,255,255,.05);border:1px solid var(--line);color:var(--fg);border-radius:10px;padding:.7rem;font-family:ui-monospace,monospace;font-size:.78rem"></textarea>
      <button class="btn full" style="margin-top:.6rem">Authenticate →</button></form>
    <?php if ($result): ?>
      <div class="<?= $result[0]==='admin'?'':'note' ?>" style="margin-top:.8rem;<?= $result[0]==='admin'?'color:#6ee7b7':'' ?>"><?= e($result[1]) ?></div>
    <?php endif; ?>
    <?php if ($flag): ?><div style="background:rgba(52,211,153,.12);border:1px dashed var(--green);color:#a7f3d0;border-radius:10px;padding:.7rem;margin-top:.6rem;font-family:ui-monospace,monospace">🚩 <?= e($flag) ?></div><?php endif; ?>
  </div>
</div>
<script>
function b64u(o){return btoa(JSON.stringify(o)).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');}
function forgeNone(){var t=b64u({alg:'none',typ:'JWT'})+'.'+b64u({sub:'attacker',role:'admin',iat:1})+'.';document.getElementById('tin').value=t;toast('Forged an alg:none admin token — now Authenticate.');}
</script>
<?php foot();
