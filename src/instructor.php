<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/hints.php';
require_login();
if (!is_instructor()) { head('Instructor'); echo '<div class="panel"><h2>🔒 Instructor only</h2><p style="color:var(--muted)">This classroom dashboard is available to the first-registered account (the instructor). You are signed in as a trainee.</p><a class="btn" href="/dashboard.php">← Back</a></div>'; foot(); exit; }

ensure_solves(); ensure_hint_unlocks();
// action: reset a trainee's progress
$note = null;
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='reset' && !empty($_POST['email'])) {
    $em = $_POST['email'];
    db()->prepare("DELETE FROM solves WHERE player=?")->execute([$em]);
    db()->prepare("DELETE FROM hint_unlocks WHERE player=?")->execute([$em]);
    $note = "Progress reset for $em.";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='settings') {
    set_setting('walkthroughs_enabled', isset($_POST['walkthroughs']) ? '1' : '0');
    set_setting('hints_enabled', isset($_POST['hints']) ? '1' : '0');
    $note = "Content settings saved.";
}
$wtOn = setting('walkthroughs_enabled','1')==='1';
$hintsOn = setting('hints_enabled','1')==='1';

$all = CATALOG(); $total = count($all);
$players = db()->query("SELECT id,email,name FROM platform_users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$totSolves = (int)db()->query("SELECT COUNT(*) FROM solves")->fetchColumn();
$active = (int)db()->query("SELECT COUNT(DISTINCT player) FROM solves")->fetchColumn();

// per-challenge solve counts + first blood
$scount=[]; foreach (db()->query("SELECT cid,COUNT(*) n FROM solves GROUP BY cid") as $r) $scount[$r['cid']]=(int)$r['n'];
$firstBlood=[]; foreach (db()->query("SELECT cid,player,MIN(ts) FROM solves GROUP BY cid") as $r) $firstBlood[$r['cid']]=$r['player'];

// SIEM summary (if table exists)
$siem=[]; try { foreach (db()->query("SELECT type,COUNT(*) n FROM siem GROUP BY type ORDER BY n DESC LIMIT 8") as $r) $siem[$r['type']]=(int)$r['n']; } catch(Throwable $e){}

head('Instructor');
?>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">🎓 Classroom · instructor console</span>
  <h1 style="font-size:2rem">Cohort overview</h1>
  <p>Monitor every trainee's progress, see which challenges are landing, and reset accounts between sessions.</p>
</div>
<?php if ($note): ?><div class="panel" style="border-color:var(--green);color:#a7f3d0;margin-top:1rem"><?= e($note) ?></div><?php endif; ?>

<div class="stat" style="margin-top:1.2rem">
  <div class="b"><b class="gradtext"><?= count($players) ?></b><span>TRAINEES</span></div>
  <div class="b"><b class="gradtext"><?= $active ?></b><span>ACTIVE</span></div>
  <div class="b"><b class="gradtext"><?= $totSolves ?></b><span>TOTAL SOLVES</span></div>
  <div class="b"><b class="gradtext"><?= $total ?></b><span>CHALLENGES</span></div>
</div>

<div class="panel" style="margin-top:1.4rem">
  <h3 style="margin-top:0">⚙️ Content controls</h3>
  <p style="color:var(--muted);margin-top:0">Decide what learning aids trainees can see. You always see everything.</p>
  <form method="post" style="display:flex;gap:1.5rem;align-items:center;flex-wrap:wrap">
    <input type="hidden" name="action" value="settings">
    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
      <input type="checkbox" name="walkthroughs" <?= $wtOn?'checked':'' ?> style="width:18px;height:18px">
      📖 Show <b>walkthroughs</b> to trainees</label>
    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
      <input type="checkbox" name="hints" <?= $hintsOn?'checked':'' ?> style="width:18px;height:18px">
      💡 Show <b>hints</b> to trainees</label>
    <button class="cta" style="padding:.5rem 1.1rem">Save</button>
    <span style="color:var(--dim);font-size:.8rem">Walkthroughs: <b style="color:<?= $wtOn?'#34d399':'#f87171' ?>"><?= $wtOn?'ON':'OFF' ?></b> · Hints: <b style="color:<?= $hintsOn?'#34d399':'#f87171' ?>"><?= $hintsOn?'ON':'OFF' ?></b></span>
  </form>
</div>

<div class="panel" style="margin-top:1.4rem">
  <h3 style="margin-top:0">👥 Trainees</h3>
  <div style="overflow:auto"><table style="width:100%;border-collapse:collapse">
    <tr style="text-align:left;color:var(--muted);font-size:.78rem"><th style="padding:.5rem">#</th><th>Name</th><th>Email</th><th>Points</th><th>Solved</th><th>Hints</th><th>Progress</th><th></th></tr>
    <?php $i=0; foreach ($players as $p): $sid=solved_ids($p['email']); $sc=count($sid); $pts=player_points($p['email']);
      $hn=(int)db()->query("SELECT COUNT(*) FROM hint_unlocks WHERE player='".$p['email']."'")->fetchColumn();
      $pc=$total?round(100*$sc/$total):0; $i++; ?>
      <tr style="border-top:1px solid var(--line)">
        <td style="padding:.55rem"><?= $i ?><?= $i===1?' 🎓':'' ?></td>
        <td><?= e($p['name']) ?></td>
        <td style="color:var(--muted)"><?= e($p['email']) ?></td>
        <td style="color:#fbbf24;font-weight:700"><?= $pts ?></td>
        <td><?= $sc ?>/<?= $total ?></td>
        <td><?= $hn ?></td>
        <td style="min-width:120px"><div style="height:7px;background:rgba(255,255,255,.08);border-radius:999px;overflow:hidden">
          <div style="height:100%;width:<?= $pc ?>%;background:linear-gradient(90deg,#3b82f6,#22d3ee)"></div></div></td>
        <td><form method="post" onsubmit="return confirm('Reset all progress for <?= e($p['email']) ?>?')">
          <input type="hidden" name="action" value="reset"><input type="hidden" name="email" value="<?= e($p['email']) ?>">
          <button class="btn" style="padding:.3rem .7rem;font-size:.75rem">Reset</button></form></td>
      </tr>
    <?php endforeach; ?>
  </table></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;margin-top:1.4rem">
  <div class="panel">
    <h3 style="margin-top:0">🎯 Challenge landing rate</h3>
    <div style="max-height:360px;overflow:auto">
    <?php foreach ($all as $c): $n=$scount[$c[0]]??0; $rate=count($players)?round(100*$n/max(1,$active)):0; ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:.4rem 0;border-bottom:1px solid var(--line)">
        <span style="font-size:.85rem"><?= $c[3] ?> <?= e($c[1]) ?></span>
        <span style="color:var(--muted);font-size:.76rem;white-space:nowrap"><?= $n ?> solves<?= isset($firstBlood[$c[0]])?' · 🩸 '.e(explode('@',$firstBlood[$c[0]])[0]):'' ?></span>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
  <div class="panel">
    <h3 style="margin-top:0">🛡 SOC — attack activity</h3>
    <?php if (!$siem): ?><p style="color:var(--muted)">No detections logged yet.</p><?php else: ?>
    <?php $mx=max($siem); foreach ($siem as $t=>$n): ?>
      <div style="margin:.5rem 0"><div style="display:flex;justify-content:space-between;font-size:.82rem"><span><?= e($t) ?></span><span style="color:var(--muted)"><?= $n ?></span></div>
        <div style="height:7px;background:rgba(255,255,255,.08);border-radius:999px;overflow:hidden;margin-top:.2rem">
          <div style="height:100%;width:<?= round(100*$n/$mx) ?>%;background:linear-gradient(90deg,#f87171,#fbbf24)"></div></div></div>
    <?php endforeach; endif; ?>
    <a class="btn" href="/soc.php" style="margin-top:.6rem">Open full SOC →</a>
  </div>
</div>
<?php foot();
