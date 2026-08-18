<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/catalog.php';
require_login();
$u = pf_user(); $solved = solved_ids($u['email']);
$CH = [
  ['chain-account-takeover','🎯 Account Takeover','From a client-side bug to full store admin.',
    [['ms-xss','Reflected XSS on the microsite steals a victim session'],['broken-admin','Ride the session into the store admin panel'],['sqli-search','Dump the user table via admin-side SQLi']],'VOLT{chain_account_takeover}'],
  ['chain-bank-heist','🏦 The Bank Heist','A leaked password becomes a drained treasury.',
    [['sqli-login','Bypass the store login & harvest reused creds'],['bank-bola','Reach the corporate treasury account (BOLA)'],['bank-neg','Exfiltrate funds with a negative-amount transfer']],'VOLT{chain_bank_heist}'],
  ['chain-ai-insider','🤖 AI Insider','Turn the copilot against the company.',
    [['ai-sys','Leak the copilot system prompt'],['ai-tool','Abuse its tools to read another customer'],['ai-disclose','Exfiltrate the full customer database']],'VOLT{chain_ai_insider}'],
  ['chain-server-compromise','💥 Server Compromise','Web bug to remote code execution.',
    [['lfi','Read files via path traversal'],['ssti','Escalate to code execution via SSTI'],['upload','Drop a webshell via file upload']],'VOLT{chain_server_compromise}'],
];
head('Campaigns');
?>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">🔗 Multi-stage campaigns</span>
  <h1 style="font-size:2rem">Attack chains</h1>
  <p>Real breaches are a chain of small bugs. Complete each phase across the apps — finish a chain to earn a bonus flag.</p>
</div>
<div style="margin-top:1.4rem;display:grid;gap:1.2rem">
<?php foreach ($CH as [$cid,$title,$story,$phases,$bonus]):
  $doneCount = count(array_filter($phases, fn($p)=>in_array($p[0],$solved,true)));
  $complete = $doneCount === count($phases); $pct = round(100*$doneCount/count($phases)); ?>
  <div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.6rem">
      <div><h2 style="margin:0"><?= e($title) ?></h2><p style="color:var(--muted);margin:.2rem 0 0"><?= e($story) ?></p></div>
      <div style="text-align:right"><b class="gradtext"><?= $pct ?>%</b><div style="color:var(--muted);font-size:.75rem"><?= $doneCount ?>/<?= count($phases) ?> phases</div></div>
    </div>
    <div style="display:flex;gap:0;margin-top:1rem;flex-wrap:wrap">
    <?php foreach ($phases as $i=>[$pid,$narr]): $ok=in_array($pid,$solved,true); $c=cat_by_id($pid); ?>
      <div style="flex:1;min-width:220px;border:1px solid <?= $ok?'var(--green)':'var(--line)' ?>;border-radius:12px;padding:.9rem;margin:.3rem;background:<?= $ok?'rgba(52,211,153,.06)':'transparent' ?>">
        <div style="font-size:.7rem;color:var(--dim)">PHASE <?= $i+1 ?></div>
        <div style="font-weight:600;margin:.2rem 0"><?= $ok?'✅':'◻' ?> <?= e($c[1]??$pid) ?></div>
        <div style="color:var(--muted);font-size:.8rem"><?= e($narr) ?></div></div>
    <?php endforeach; ?>
    </div>
    <?php if ($complete): ?>
      <div style="background:rgba(52,211,153,.12);border:1px dashed var(--green);color:#a7f3d0;border-radius:10px;padding:.7rem 1rem;margin-top:.8rem;font-family:ui-monospace,monospace">
        🏆 Chain complete! Bonus flag: <b><?= e($bonus) ?></b> — submit it for extra points.</div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
<?php foot();
