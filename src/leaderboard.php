<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/catalog.php';
require_login();
$u = pf_user();
$lb = leaderboard(50);
$total = count(CATALOG());
head('Leaderboard');
?>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">🏆 Global leaderboard</span>
  <h1 style="font-size:2rem">Top hackers on the range</h1>
  <p>Earn points by capturing flags. <?= count($lb) ?> players have scored so far.</p>
</div>
<div class="panel" style="margin-top:1.4rem;padding:0;overflow:hidden">
  <table style="width:100%;border-collapse:collapse">
    <tr style="text-align:left;color:var(--muted);font-size:.72rem;text-transform:uppercase">
      <th style="padding:.9rem 1.2rem">Rank</th><th>Player</th><th>Solved</th><th style="text-align:right;padding-right:1.4rem">Points</th></tr>
    <?php foreach ($lb as $i=>$r): $me=$r['email']===$u['email']; $medal=['🥇','🥈','🥉'][$i]??('#'.($i+1)); ?>
      <tr style="border-top:1px solid var(--line);<?= $me?'background:rgba(34,211,238,.08)':'' ?>">
        <td style="padding:.85rem 1.2rem;font-weight:800;font-size:1.05rem"><?= $medal ?></td>
        <td style="font-weight:600"><?= e($r['name']) ?><?= $me?' <span style="color:var(--cyan);font-size:.75rem">(you)</span>':'' ?>
          <div style="color:var(--dim);font-size:.75rem"><?= e($r['email']) ?></div></td>
        <td style="color:var(--muted)"><?= $r['solved'] ?>/<?= $total ?></td>
        <td style="text-align:right;padding-right:1.4rem;font-weight:800;color:#fbbf24"><?= $r['points'] ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$lb): ?><tr><td colspan="4" style="padding:1.4rem;text-align:center;color:var(--muted)">No scores yet — be the first! Go capture a flag.</td></tr><?php endif; ?>
  </table>
</div>
<?php foot();
