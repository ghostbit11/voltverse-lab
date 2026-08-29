<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/hints.php';
require_login();
$u = pf_user();
$pmsg = null; $pbad = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'account') {
    $newName = trim($_POST['name'] ?? '');
    $newPw   = $_POST['password'] ?? '';
    $curPw   = $_POST['current'] ?? '';
    // verify current password before any change
    $row = db()->prepare("SELECT pass FROM platform_users WHERE email=?"); $row->execute([$u['email']]); $hash = $row->fetchColumn();
    if (!$hash || !password_verify($curPw, $hash)) { $pmsg = 'Current password is incorrect.'; $pbad = true; }
    else {
        if ($newName !== '' && $newName !== $u['name']) { set_user_name($u['email'], $newName); $_SESSION['pf']['name'] = $newName; $u = pf_user(); }
        if ($newPw !== '') { if (strlen($newPw) < 3) { $pmsg = 'New password too short.'; $pbad = true; } else set_user_password($u['email'], $newPw); }
        if (!$pbad) $pmsg = 'Account updated.';
    }
}
$all = CATALOG(); $total = count($all);
$solved = solved_ids($u['email']); $done = count($solved);
$pts = player_points($u['email']); $rank = player_rank($u['email']); $pct = $total?round(100*$done/$total):0;
ensure_hint_unlocks();
$hu = db()->prepare("SELECT COUNT(*) FROM hint_unlocks WHERE player=?"); $hu->execute([$u['email']]); $hintsUsed=(int)$hu->fetchColumn();
// solved timeline
$rows = db()->prepare("SELECT cid,ts FROM solves WHERE player=? ORDER BY ts DESC"); $rows->execute([$u['email']]);
$timeline = $rows->fetchAll(PDO::FETCH_ASSOC);
// per-category
$cat=[]; foreach($all as $c){ $k=$c[4]; $cat[$k]=$cat[$k]??['t'=>0,'s'=>0]; $cat[$k]['t']++; if(in_array($c[0],$solved,true))$cat[$k]['s']++; }
arsort($cat);
$initials = strtoupper(substr(preg_replace('/[^a-zA-Z]/','',$u['name']).'X',0,2));
$tier = level_name($pts);
head('Profile');
?>
<div class="hero fadeup" style="display:grid;grid-template-columns:auto 1fr auto;gap:1.5rem;align-items:center">
  <div style="width:96px;height:96px;border-radius:24px;display:grid;place-items:center;font-size:2.2rem;font-weight:900;background:var(--grad);color:#04121f"><?= e($initials) ?></div>
  <div>
    <span class="eyebrow">● <?= e($tier) ?></span>
    <h1 style="margin:.2rem 0"><?= e($u['name']) ?></h1>
    <p style="margin:0;color:var(--muted)"><?= e($u['email']) ?> · Rank #<?= $rank ?> · joined VoltVerse</p>
  </div>
  <div style="text-align:center">
    <div style="width:120px;height:120px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(#22d3ee <?= round($pct*3.6) ?>deg,rgba(255,255,255,.06) 0)">
      <div style="width:92px;height:92px;border-radius:50%;background:#0a1020;display:grid;place-items:center">
        <div><div class="gradtext" style="font-size:1.5rem;font-weight:900"><?= $pct ?>%</div><div style="color:var(--muted);font-size:.65rem">COMPLETE</div></div></div></div>
  </div>
</div>

<div class="stat" style="margin-top:1.2rem">
  <div class="b"><b class="gradtext"><?= $pts ?></b><span>POINTS</span></div>
  <div class="b"><b class="gradtext">#<?= $rank ?></b><span>RANK</span></div>
  <div class="b"><b class="gradtext"><?= $done ?>/<?= $total ?></b><span>SOLVED</span></div>
  <div class="b"><b class="gradtext">🔥 <?= player_streak($u['email']) ?></b><span>DAY STREAK</span></div>
  <div class="b"><b class="gradtext"><?= $hintsUsed ?></b><span>HINTS USED</span></div>
</div>

<div class="panel" style="margin-top:1.4rem">
  <h3 style="margin-top:0">Account settings</h3>
  <?php if ($pmsg): ?><div style="border:1px solid <?= $pbad?'rgba(242,86,75,.4)':'var(--accent-line)' ?>;color:<?= $pbad?'#f3a09a':'#9db8ff' ?>;background:<?= $pbad?'rgba(242,86,75,.08)':'var(--accent-soft)' ?>;border-radius:9px;padding:.6rem .9rem;margin-bottom:.8rem;font-size:.88rem"><?= e($pmsg) ?></div><?php endif; ?>
  <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;max-width:640px">
    <input type="hidden" name="action" value="account">
    <div><label>Display name</label><input name="name" value="<?= e($u['name']) ?>"></div>
    <div><label>Email <span style="color:var(--dim);text-transform:none">(can't change)</span></label><input value="<?= e($u['email']) ?>" disabled></div>
    <div><label>New password <span style="color:var(--dim);text-transform:none">(leave blank to keep)</span></label><input type="password" name="password" placeholder="••••••••"></div>
    <div><label>Current password <span style="color:var(--dim);text-transform:none">(required to save)</span></label><input type="password" name="current" placeholder="verify it's you" required></div>
    <div style="grid-column:1/3"><button class="btn">Save changes</button></div>
  </form>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;margin-top:1.4rem">
  <div class="panel">
    <h3 style="margin-top:0">📊 Skill breakdown</h3>
    <?php foreach ($cat as $k=>$d): $p=$d['t']?round(100*$d['s']/$d['t']):0; ?>
      <div style="margin:.5rem 0">
        <div style="display:flex;justify-content:space-between;font-size:.82rem"><span><?= e($k) ?></span><span style="color:var(--muted)"><?= $d['s'] ?>/<?= $d['t'] ?></span></div>
        <div style="height:7px;background:rgba(255,255,255,.08);border-radius:999px;overflow:hidden;margin-top:.2rem">
          <div style="height:100%;width:<?= $p ?>%;background:linear-gradient(90deg,#3b82f6,#22d3ee)"></div></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="panel">
    <h3 style="margin-top:0">🕓 Recent solves</h3>
    <?php if (!$timeline): ?><p style="color:var(--muted)">No solves yet — go capture a flag!</p><?php endif; ?>
    <div style="max-height:280px;overflow:auto">
    <?php foreach ($timeline as $t): $c=cat_by_id($t['cid']); if(!$c)continue; ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:.45rem 0;border-bottom:1px solid var(--line)">
        <span><?= $c[3] ?> <?= e($c[1]) ?></span>
        <span style="color:var(--dim);font-size:.74rem;white-space:nowrap"><?= e($t['ts']) ?> · <b style="color:#fbbf24"><?= $c[7] ?></b></span></div>
    <?php endforeach; ?>
    </div>
  </div>
</div>

<h2 style="margin:2rem 0 1rem;font-size:1.3rem">🎓 Certificate of completion</h2>
<div id="cert" style="background:linear-gradient(135deg,#0b1224,#111a33);border:2px solid #22d3ee;border-radius:18px;padding:2.4rem;position:relative;overflow:hidden">
  <div style="position:absolute;inset:0;background:radial-gradient(circle at 20% 10%,rgba(34,211,238,.14),transparent 40%);pointer-events:none"></div>
  <div style="text-align:center;position:relative">
    <div style="letter-spacing:.35em;color:#22d3ee;font-size:.8rem;font-weight:700">VOLTVERSE SECURITY RANGE</div>
    <div style="font-size:1.6rem;font-weight:800;margin:.6rem 0">Certificate of Achievement</div>
    <p style="color:var(--muted);margin:0">This certifies that</p>
    <div class="gradtext" style="font-size:2rem;font-weight:900;margin:.3rem 0"><?= e($u['name']) ?></div>
    <p style="color:var(--muted);max-width:560px;margin:.4rem auto">has completed <b style="color:#fff"><?= $done ?></b> of <b style="color:#fff"><?= $total ?></b> security challenges
      (<?= $pct ?>%), earning <b style="color:#fff"><?= $pts ?></b> points and the rank of <b style="color:#fff"><?= e($tier) ?></b>.</p>
    <div style="display:flex;justify-content:space-between;max-width:560px;margin:1.6rem auto 0;font-size:.78rem;color:var(--dim)">
      <div style="text-align:left"><div style="color:#fff;border-top:1px solid var(--line);padding-top:.3rem">VoltVerse Training</div>Issuing authority</div>
      <div style="text-align:right"><div style="color:#fff;border-top:1px solid var(--line);padding-top:.3rem">ID: VV-<?= strtoupper(substr(md5($u['email']),0,8)) ?></div>Verification</div>
    </div>
  </div>
</div>
<div style="margin-top:1rem"><a class="cta" href="/certificate.php">⬇ Download PDF certificate</a>
  <button class="btn" style="margin-left:.5rem" onclick="window.print()">🖨 Print</button>
  <a class="btn" href="/leaderboard.php" style="margin-left:.5rem">View leaderboard →</a></div>
<style>@media print{.nav,.cta,.btn,#bg,canvas{display:none!important}body{background:#fff}#cert{border-color:#0b1224}}</style>
<?php foot();
