<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/catalog.php';
require_login();
$u = pf_user();

/* ---- Superadmin sees a CONFIGURATION console, never a solving surface ---- */
if (is_superadmin()) {
    $apps = all_apps();
    $snote = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'labs') {
        $on = $_POST['lab'] ?? [];
        foreach ($apps as $app) set_setting('lab:'.$app, in_array($app, $on, true) ? '1' : '0');
        $snote = 'Lab configuration saved.';
    }
    $meta = cat_apps();
    $enabledCount = count(array_filter($apps, 'lab_enabled'));
    $userCount = (int)db()->query("SELECT COUNT(*) FROM platform_users")->fetchColumn();
    $memberCount = (int)db()->query("SELECT COUNT(*) FROM platform_users WHERE role='member'")->fetchColumn();
    head('Lab control');
    ?>
    <div class="hero fadeup" style="padding:1.8rem 1.9rem">
      <span class="eyebrow">👑 Super administrator · configuration</span>
      <h1 style="font-size:1.8rem">Lab control center</h1>
      <p>You manage the range — decide which labs are live for your users, and administer accounts. Solving is for members and admins.</p>
      <div style="margin-top:1rem"><a class="cta" href="/admin.php">Manage users →</a>
        <a class="btn" href="/leaderboard.php" style="margin-left:.5rem">Leaderboard</a>
        <a class="btn" href="/soc.php" style="margin-left:.5rem">🛡 SOC</a></div>
    </div>
    <div class="stat" style="margin-top:1.2rem">
      <div class="b"><b class="gradtext"><?= $userCount ?></b><span>Users</span></div>
      <div class="b"><b class="gradtext"><?= $memberCount ?></b><span>Members</span></div>
      <div class="b"><b class="gradtext"><?= $enabledCount ?>/<?= count($apps) ?></b><span>Labs live</span></div>
      <div class="b"><b class="gradtext"><?= count(CATALOG()) ?></b><span>Challenges</span></div>
    </div>
    <?php if ($snote): ?><div class="panel" style="margin-top:1rem;border-color:var(--accent-line);color:#7ee0a0">✓ <?= e($snote) ?></div><?php endif; ?>
    <div class="panel" style="margin-top:1.4rem">
      <h3 style="margin-top:0">Labs — turn any target on or off</h3>
      <p style="color:var(--muted);margin-top:0;font-size:.9rem">Disabled labs disappear from every user's dashboard, challenges and assignments, and their pages become inaccessible.</p>
      <form method="post"><input type="hidden" name="action" value="labs">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:.7rem;margin-top:.6rem">
        <?php foreach ($apps as $app): $on=lab_enabled($app); $m=$meta[$app]??['icon'=>'🔬','total'=>0]; ?>
          <label style="display:flex;align-items:center;gap:.8rem;padding:.8rem 1rem;border:1px solid <?= $on?'var(--accent-line)':'var(--line)' ?>;border-radius:11px;background:<?= $on?'var(--accent-soft)':'var(--surface2)' ?>;cursor:pointer;text-transform:none;letter-spacing:0;margin:0">
            <input type="checkbox" name="lab[]" value="<?= e($app) ?>" style="width:18px;height:18px" <?= $on?'checked':'' ?>>
            <span style="font-size:1.4rem"><?= $m['icon'] ?></span>
            <span style="flex:1"><b style="font-size:.95rem"><?= e($app) ?></b><br><span style="color:var(--dim);font-size:.76rem"><?= $m['total'] ?> challenges · <span style="color:<?= $on?'#7ee0a0':'#f3a09a' ?>"><?= $on?'LIVE':'OFF' ?></span></span></span>
          </label>
        <?php endforeach; ?>
        </div>
        <button class="btn" style="margin-top:1rem">Save lab configuration</button>
      </form>
    </div>
    <?php foot(); exit;
}

$solved = solved_ids($u['email']);
$assigned = assigned_ids($u['email']);
$restricted = !is_admin_user() && !empty($assigned);   // member with a training plan → scope everything to it
$catalog = catalog_enabled();                            // hide any lab the superadmin turned off
if ($restricted) $catalog = array_values(array_filter($catalog, fn($c)=>in_array($c[0],$assigned,true)));
$vis = array_map(fn($c)=>$c[0], $catalog);
$total = count($catalog); $done = count(array_intersect($solved,$vis));
$pts = player_points($u['email']); $rank = player_rank($u['email']);
$pct = $total ? round(100*$done/$total) : 0;
$deg = round($pct*3.6);

$base = ['Voltmart'=>'/store/','Aurora Bank'=>'/bank/','VoltID'=>'/jwt/','Voltmart Copilot'=>'/ai/','VoltData'=>'/graphql/','VoltConnect'=>'/oauth/','VoltSync'=>'/deserial/','VoltBook Microsite'=>'/product-site/','VoltCorp Website'=>'/corp/','Voltmart API'=>'/api/','Campaigns'=>'/campaigns.php'];
$per = []; foreach ($catalog as $c){ $per[$c[2]]=$per[$c[2]]??['icon'=>$c[3],'t'=>0,'s'=>0]; $per[$c[2]]['t']++; if(in_array($c[0],$solved,true))$per[$c[2]]['s']++; }

$ach = [
  ['🎬','Getting Started','Solve your first challenge',$done>=1],
  ['🩸','First Blood','Be first to solve a challenge',(bool)db()->query("SELECT 1 FROM solves WHERE player='".$u['email']."' LIMIT 1")->fetch()],
  ['💉','Injector','Solve 3 injection bugs',count(array_filter(CATALOG(),fn($c)=>in_array($c[0],$solved,true)&&in_array($c[4],['Injection','XSS'])))>=3],
  ['🤖','AI Breaker','Solve an LLM challenge',(bool)count(array_filter(CATALOG(),fn($c)=>in_array($c[0],$solved,true)&&$c[4]==='LLM'))],
  ['🏆','Halfway','Solve 50% of challenges',$done>=$total/2],
  ['👑','Completionist','Solve everything',$done>=$total&&$total>0],
];
head('Dashboard');
?>
<div class="hero fadeup" style="display:grid;grid-template-columns:1fr auto;gap:1.5rem;align-items:center">
  <div>
    <span class="eyebrow">● Range online · welcome <?= e($u['name']) ?></span>
    <h1>Keep hacking.<br><span class="gradtext"><?= e(level_name($pts)) ?> · <?= $pts ?> pts</span></h1>
    <p>Break into live targets, capture flags, and climb the leaderboard. You've solved <b><?= $done ?></b> of <b><?= $total ?></b> challenges.</p>
    <div class="stat">
      <div class="b"><b class="gradtext"><?= $pts ?></b><span>POINTS</span></div>
      <div class="b"><b class="gradtext">#<?= $rank ?></b><span>RANK</span></div>
      <div class="b"><b class="gradtext"><?= $done ?>/<?= $total ?></b><span>SOLVED</span></div>
      <div class="b"><b class="gradtext">🔥 <?= player_streak($u['email']) ?></b><span>DAY STREAK</span></div>
    </div>
    <div style="margin-top:1.2rem"><a class="cta" href="/challenges.php">View all challenges →</a>
      <button class="btn" style="margin-left:.5rem" onclick="startTour()">🎓 Take the tour</button></div>
  </div>
  <div style="text-align:center">
    <div style="width:170px;height:170px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(#22d3ee <?= $deg ?>deg,rgba(255,255,255,.06) <?= $deg ?>deg)">
      <div style="width:130px;height:130px;border-radius:50%;background:#0a1020;display:grid;place-items:center">
        <div><div style="font-size:2rem;font-weight:900" class="gradtext"><?= $pct ?>%</div><div style="color:var(--muted);font-size:.72rem">COMPLETE</div></div></div></div>
  </div>
</div>

<?php $myAssigned = array_values(array_filter(assigned_ids($u['email']), fn($id)=>($c=cat_by_id($id)) && lab_enabled($c[2])));
if ($myAssigned): $adone = count(array_filter($myAssigned, fn($id)=>in_array($id,$solved,true))); ?>
<div class="panel fadeup" style="margin-top:1.6rem;border-color:var(--accent-line)">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.6rem">
    <div><span class="eyebrow">Assigned to you</span>
      <h3 style="margin:.3rem 0 0">Your training plan</h3></div>
    <div style="text-align:right"><b class="gradtext" style="font-size:1.3rem"><?= $adone ?>/<?= count($myAssigned) ?></b><div style="color:var(--dim);font-size:.72rem">COMPLETED</div></div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:.5rem;margin-top:.9rem">
  <?php foreach ($myAssigned as $aid): $ac=cat_by_id($aid); if(!$ac)continue; $ok=in_array($aid,$solved,true); $ah=$base[$ac[2]]??'#'; ?>
    <a href="<?= e($ah) ?>" style="display:flex;align-items:center;gap:.6rem;padding:.6rem .8rem;border:1px solid <?= $ok?'var(--green)':'var(--line)' ?>;border-radius:10px;background:<?= $ok?'rgba(67,192,106,.06)':'var(--surface2)' ?>;color:var(--fg)">
      <span style="font-size:1.1rem"><?= $ok?'✅':$ac[3] ?></span>
      <span style="flex:1;min-width:0"><span style="font-size:.85rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($ac[1]) ?></span>
        <span style="color:var(--dim);font-size:.72rem"><?= e($ac[2]) ?> · <?= $ac[7] ?> pts</span></span></a>
  <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php $dc0 = daily_challenge(); $dcSolved = in_array($dc0[0],$solved,true); $dcHref = $base[$dc0[2]] ?? '#'; ?>
<div class="panel fadeup" style="margin-top:1.6rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;border:1px solid rgba(251,191,36,.35);background:rgba(251,191,36,.05)">
  <div><span class="eyebrow" style="color:#fbbf24">⭐ CHALLENGE OF THE DAY</span>
    <h3 style="margin:.3rem 0"><?= $dc0[3] ?> <?= e($dc0[1]) ?> <?= $dcSolved?'<span style="color:var(--green);font-size:.9rem">✓ solved</span>':'' ?></h3>
    <p style="color:var(--muted);margin:0"><?= e($dc0[2]) ?> · <?= e($dc0[4]) ?> · <b style="color:#fbbf24"><?= $dc0[7] ?> pts</b></p></div>
  <a class="cta" href="<?= e($dcHref) ?>"><?= $dcSolved?'Revisit':'Attempt now' ?> →</a>
</div>

<h2 style="margin:2rem 0 1rem;font-size:1.4rem">Your targets</h2>
<div class="grid">
<?php $i=0; foreach ($per as $name=>$d): $p = $d['t']?round(100*$d['s']/$d['t']):0; $href=$base[$name]??'#'; ?>
  <a class="appcard fadeup d<?= (($i++)%4)+1 ?>" href="<?= e($href) ?>">
    <span class="live">LIVE</span><div class="ico"><?= $d['icon'] ?></div>
    <h3><?= e($name) ?></h3>
    <p style="min-height:auto"><?= $d['s'] ?>/<?= $d['t'] ?> solved</p>
    <div style="height:8px;background:rgba(255,255,255,.08);border-radius:999px;overflow:hidden;margin:.3rem 0 .2rem">
      <div style="height:100%;width:<?= $p ?>%;background:linear-gradient(90deg,#3b82f6,#22d3ee)"></div></div>
    <div style="color:var(--muted);font-size:.75rem"><?= $p==100?'✓ Completed':'Continue →' ?></div>
  </a>
<?php endforeach; ?>
</div>

<h2 style="margin:2rem 0 1rem;font-size:1.4rem">Achievements</h2>
<div style="display:flex;gap:.8rem;flex-wrap:wrap">
<?php foreach ($ach as [$ic,$nm,$ds,$got]): ?>
  <div style="text-align:center;min-width:120px;padding:1rem;border:1px solid <?= $got?'var(--green)':'var(--line)' ?>;border-radius:16px;background:<?= $got?'rgba(52,211,153,.07)':'var(--glass)' ?>;opacity:<?= $got?1:.45 ?>" title="<?= e($ds) ?>">
    <div style="font-size:1.8rem"><?= $ic ?></div><div style="font-size:.78rem;margin-top:.3rem"><?= e($nm) ?></div>
    <div style="font-size:.62rem;color:var(--dim)"><?= $got?'unlocked':'locked' ?></div></div>
<?php endforeach; ?>
</div>

<div id="tour" style="display:none;position:fixed;inset:0;z-index:60;background:rgba(3,7,18,.82);backdrop-filter:blur(6px);place-items:center">
  <div style="max-width:520px;margin:0 1rem;background:var(--glass);border:1px solid var(--line);border-radius:20px;padding:2rem;box-shadow:0 30px 80px rgba(0,0,0,.6)">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <span class="eyebrow">GUIDED TOUR · <span id="tstep">1</span>/6</span>
      <span onclick="endTour()" style="cursor:pointer;color:var(--muted)">Skip ✕</span>
    </div>
    <div id="tico" style="font-size:2.6rem;margin:.6rem 0"></div>
    <h2 id="ttitle" style="margin:.2rem 0"></h2>
    <p id="tbody" style="color:var(--muted)"></p>
    <div style="display:flex;gap:.6rem;justify-content:flex-end;margin-top:1.2rem">
      <button class="btn" onclick="tourPrev()" id="tprev">← Back</button>
      <button class="cta" onclick="tourNext()" id="tnext">Next →</button>
    </div>
  </div>
</div>
<script>
var TOUR=[
 ['🛰️','Welcome to VoltVerse','A live cyber range with 11 realistic targets — a store, a bank, an AI copilot, JWT SSO, GraphQL, OAuth, APIs and more. Each app hides multiple real vulnerabilities.'],
 ['🎯','Pick a target','On "Your targets", click any card marked LIVE to open the real app and start hacking. Nothing here is a toy form — treat it like the real thing.'],
 ['🎚️','Choose a difficulty','Like bWAPP, every bug scales: Low → Medium → High → Secure. Start at Low, then raise it to make the same vuln harder — or study the fixed code at Secure.'],
 ['🚩','Capture & submit flags','Exploit a bug and a flag like VOLT{...} appears. Hit "🚩 Submit flag" to score points, earn first-blood and climb the leaderboard.'],
 ['🧭','Learn as you go','Stuck? The Challenges page has progressive Hints (small point cost) and full Walkthroughs. Chain bugs across apps in Campaigns, and watch your attacks light up the 🛡 SOC.'],
 ['🏆','Track your progress','Your Profile shows skill breakdowns, solve history and a printable certificate. Ready? Go break something.'],
];
var ti=0;
function renderTour(){var t=TOUR[ti];document.getElementById('tico').textContent=t[0];document.getElementById('ttitle').textContent=t[1];
 document.getElementById('tbody').textContent=t[2];document.getElementById('tstep').textContent=ti+1;
 document.getElementById('tprev').style.visibility=ti===0?'hidden':'visible';
 document.getElementById('tnext').textContent=ti===TOUR.length-1?'Start hacking →':'Next →';}
function startTour(){ti=0;renderTour();document.getElementById('tour').style.display='grid';}
function tourNext(){if(ti>=TOUR.length-1){endTour();return;}ti++;renderTour();}
function tourPrev(){if(ti>0){ti--;renderTour();}}
function endTour(){document.getElementById('tour').style.display='none';document.cookie='vv_onboarded=1;path=/;max-age=31536000';}
<?php if (empty($_COOKIE['vv_onboarded'])): ?>startTour();<?php endif; ?>
</script>
<?php foot();
