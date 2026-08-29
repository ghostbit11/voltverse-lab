<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/walkthroughs.php';
require_once __DIR__ . '/inc/hints.php';
require_login();
$u = pf_user();
$solved = solved_ids($u['email']);
$assigned = assigned_ids($u['email']);
// Members with an assigned training plan see ONLY their assigned tests. Admins see everything.
$restricted = !is_admin_user() && !empty($assigned);
$all = CATALOG();
if ($restricted) $all = array_values(array_filter($all, fn($c)=>in_array($c[0],$assigned,true)));
$visibleIds = array_map(fn($c)=>$c[0], $all);
$done = count(array_intersect($solved, $visibleIds)); $total = count($all);
$apps = array_values(array_unique(array_map(fn($c)=>$c[2], $all)));
$dl = [1=>'Easy',2=>'Medium',3=>'Hard',4=>'Expert'];
$showWt = is_admin_user() || member_wt_on($u['email']);
$showHints = is_admin_user() || member_hints_on($u['email']);
$dc = [1=>'#34d399',2=>'#fbbf24',3=>'#fb923c',4=>'#f87171'];
$base = ['Voltmart'=>'/store/','Aurora Bank'=>'/bank/','VoltID'=>'/jwt/','Voltmart Copilot'=>'/ai/','VoltData'=>'/graphql/','VoltConnect'=>'/oauth/','VoltSync'=>'/deserial/','VoltBook Microsite'=>'/product-site/','VoltCorp Website'=>'/corp/','Voltmart API'=>'/api/','Campaigns'=>'/campaigns.php'];
head('Challenges');
?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1rem">
  <div><h1 style="margin:0;font-size:1.7rem"><?= $restricted ? 'Your assigned tests' : 'All challenges' ?></h1>
    <p style="color:var(--muted);margin:.3rem 0 0"><?= $done ?>/<?= $total ?> solved · <?= player_points($u['email']) ?> pts.
      <?= $restricted ? 'These were assigned to you by your admin. ' : '' ?>Find a flag, then hit <b>🚩 Submit flag</b>.</p></div>
  <button class="sfbtn" style="padding:.6rem 1.1rem;font-size:.9rem" onclick="openFlag()">🚩 Submit flag</button>
</div>
<div class="filters" style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.2rem">
  <span class="chipf on" data-f="all" onclick="filt(this,'all')">All (<?= $total ?>)</span>
  <?php foreach ($apps as $a): $n=count(array_filter($all,fn($c)=>$c[2]===$a)); ?>
    <span class="chipf" data-f="<?= e($a) ?>" onclick="filt(this,'<?= e($a) ?>')"><?= e($a) ?> (<?= $n ?>)</span>
  <?php endforeach; ?>
</div>
<style>
.chipf{padding:.4rem .9rem;border-radius:999px;background:var(--glass);border:1px solid var(--line);color:var(--muted);cursor:pointer;font-size:.85rem}
.chipf.on{background:rgba(34,211,238,.14);color:#fff;border-color:var(--cyan)}
.chrow{display:grid;grid-template-columns:auto 1fr auto auto auto;gap:1rem;align-items:center;padding:.9rem 1.1rem;border:1px solid var(--line);border-radius:14px;background:var(--glass);margin-bottom:.6rem}
.chrow.done{border-color:rgba(52,211,153,.4);background:rgba(52,211,153,.05)}
.chrow .badge{font-size:.66rem;font-weight:700;color:var(--cyan);background:rgba(34,211,238,.1);border:1px solid rgba(34,211,238,.3);border-radius:6px;padding:2px 7px}
.chrow .pts{font-weight:800;color:#fbbf24}
.witem details{margin:-.3rem 0 .6rem;border:1px solid var(--line);border-radius:12px;background:rgba(255,255,255,.02);padding:.2rem .9rem}
.witem summary{cursor:pointer;color:var(--muted);font-size:.82rem;padding:.5rem 0;list-style:none;user-select:none}
.witem summary::-webkit-details-marker{display:none}
.witem summary:hover{color:#fff}
.witem ol{margin:.2rem 0 .8rem 1.1rem;color:var(--fg);font-size:.85rem;line-height:1.7}
.witem ol code, .witem p code{background:rgba(34,211,238,.12);color:#7dd3fc;padding:1px 5px;border-radius:5px;font-size:.82em}
.wtlv{display:flex;gap:.7rem;align-items:flex-start;padding:.5rem 0;border-top:1px solid var(--line)}
.wtlv:first-child{border-top:0}.wtlv p{margin:.15rem 0;font-size:.85rem;color:var(--fg)}
.wtlv ol{margin:.1rem 0 .3rem 1rem}
.lvtag{flex:0 0 auto;font-size:.62rem;font-weight:800;letter-spacing:.05em;padding:3px 8px;border-radius:6px;margin-top:.2rem;white-space:nowrap}
.lvtag.low{background:rgba(52,211,153,.14);color:#34d399;border:1px solid rgba(52,211,153,.35)}
.lvtag.med{background:rgba(251,146,60,.14);color:#fb923c;border:1px solid rgba(251,146,60,.35)}
.lvtag.sec{background:rgba(96,165,250,.14);color:#60a5fa;border:1px solid rgba(96,165,250,.35)}
.witem .tools{display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;margin:-.15rem 0 .55rem}
.hintbtn{cursor:pointer;font-size:.8rem;color:#fbbf24;background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.3);border-radius:9px;padding:.35rem .8rem}
.hintbtn:hover{background:rgba(251,191,36,.16)}.hintbtn[disabled]{opacity:.5;cursor:default}
.hintbox{font-size:.84rem;color:#fde68a;background:rgba(251,191,36,.06);border-left:2px solid #fbbf24;border-radius:6px;padding:.5rem .8rem;margin:.35rem 0}
.hintbox code{background:rgba(251,191,36,.16);color:#fde68a;padding:1px 5px;border-radius:5px}
</style>
<div id="list">
<?php foreach ($all as $c): $s = in_array($c[0],$solved,true); $wt = walkthrough($c[0]);
  $hints = hints_for($c[0]); $unl = $hints ? unlocked_hints($u['email'],$c[0]) : []; ?>
  <div class="witem" data-app="<?= e($c[2]) ?>">
  <div class="chrow <?= $s?'done':'' ?>">
    <div style="font-size:1.4rem"><?= $s?'✅':'<span style=\"color:var(--dim)\">◻</span>' ?></div>
    <div><div style="font-weight:600"><?= e($c[1]) ?></div>
      <div style="color:var(--muted);font-size:.8rem"><?= $c[3] ?> <?= e($c[2]) ?> · <?= e($c[9]) ?></div></div>
    <div><span class="badge"><?= e($c[4]) ?></span> <span class="badge" style="color:#a78bfa;border-color:rgba(167,130,255,.3);background:rgba(167,130,255,.1)"><?= e($c[5]) ?></span></div>
    <div style="color:<?= $dc[$c[6]] ?>;font-weight:700;font-size:.82rem"><?= $dl[$c[6]] ?></div>
    <div style="display:flex;align-items:center;gap:.8rem"><span class="pts"><?= $c[7] ?></span>
      <a class="btn sfbtn" style="padding:.4rem .8rem;background:var(--grad);color:#04121f" href="<?= e($base[$c[2]]??'#') ?>">Open →</a></div>
  </div>
  <?php if ($hints && $showHints): ?>
  <div class="tools">
    <button class="hintbtn" data-cid="<?= e($c[0]) ?>" data-n="<?= count($hints) ?>" onclick="getHint(this)">💡 Hint (−<?= HINT_COST ?> pts)</button>
    <span style="color:var(--dim);font-size:.74rem">costs <?= HINT_COST ?> pts per hint</span>
    <div class="hintwrap" style="width:100%"><?php foreach ($unl as $i) echo '<div class="hintbox">💡 '.$hints[$i].'</div>'; ?></div>
  </div>
  <?php endif; ?>
  <?php if ($wt && $showWt): $mh=wt_mh($c[0]); $sec=wt_sec($c[0]); ?>
  <details><summary>📖 Walkthrough — solution by difficulty<?= is_instructor() && !$wtEnabled ? ' <span style="color:#fbbf24">(hidden from trainees)</span>' : '' ?></summary>
    <div class="wtlv"><span class="lvtag low">LOW</span>
      <ol><?php foreach ($wt as $step) echo '<li>'.$step.'</li>'; ?></ol></div>
    <div class="wtlv"><span class="lvtag med">MEDIUM / HIGH</span>
      <p><?= $mh !== '' ? $mh : 'No extra filter at these tiers — the Low payload above still works.' ?></p></div>
    <?php if ($sec !== ''): ?><div class="wtlv"><span class="lvtag sec">SECURE</span>
      <p><?= $sec ?> <span style="color:var(--dim)">(not exploitable — this is the reference fix)</span></p></div><?php endif; ?>
  </details>
  <?php elseif ($wt && !$showWt): ?>
  <div style="font-size:.8rem;color:var(--dim);padding:.2rem 0 .5rem">📖 Walkthroughs are currently disabled by your instructor.</div>
  <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
<script>function filt(el,f){document.querySelectorAll('.chipf').forEach(x=>x.classList.remove('on'));el.classList.add('on');
document.querySelectorAll('.witem').forEach(r=>{r.style.display=(f==='all'||r.dataset.app===f)?'block':'none'})}
function getHint(btn){var cid=btn.dataset.cid,total=+btn.dataset.n,wrap=btn.parentNode.querySelector('.hintwrap');
  var shown=wrap.querySelectorAll('.hintbox').length; if(shown>=total){btn.disabled=true;btn.textContent='All hints shown';return;}
  var fd=new URLSearchParams();fd.append('cid',cid);fd.append('idx',shown);
  fetch('/hint.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:fd}).then(r=>r.json()).then(d=>{
    if(!d.ok)return; var el=document.createElement('div');el.className='hintbox';el.innerHTML='💡 '+d.hint;wrap.appendChild(el);
    if(d.charged&&typeof toast==='function')toast('Hint unlocked · −'+d.cost+' pts (score: '+d.points+')');
    if(shown+1>=total){btn.disabled=true;btn.textContent='All hints shown';}
  });}</script>
<?php foot();
