<?php
require_once __DIR__ . '/inc/layout.php';
require_login();
if (isset($_GET['set'])) {
    $s = in_array($_GET['set'], ['low','medium','high','secure']) ? $_GET['set'] : 'low';
    setcookie('vv_level', $s, 0, '/');
    header('Location: /level.php'); exit;
}
head('Difficulty');
$cur = level();
$levels = [
  ['low','⚠ Low','Textbook vulnerabilities — no defences. Easiest to exploit.'],
  ['medium','◐ Medium','Naive defences (blacklists, weak filters) that can be bypassed.'],
  ['high','◕ High','Stronger but incomplete protections with a gap to find.'],
  ['secure','🛡 Secure','Correct, fixed implementation — a reference (not exploitable).'],
];
?>
<div class="panel" style="max-width:720px;margin:1rem auto">
  <h2>🎚 Difficulty level</h2>
  <p style="color:var(--muted)">Sets the security level across every app — like bWAPP. Raise it to make the
  same bugs harder, or switch to Secure to see the fixed code behaviour.</p>
  <div style="display:grid;gap:.7rem;margin-top:1rem">
  <?php foreach ($levels as [$k,$label,$desc]): ?>
    <a href="/level.php?set=<?= $k ?>" style="display:flex;align-items:center;gap:1rem;padding:1rem;border-radius:12px;
       border:1px solid <?= $cur===$k?'var(--accent2)':'var(--line)' ?>;background:<?= $cur===$k?'rgba(34,211,238,.08)':'transparent' ?>;color:var(--fg)">
      <span class="lvlpill lvl-<?= $k ?>" style="min-width:84px;text-align:center"><?= e(strtoupper($k)) ?></span>
      <span><b><?= e($label) ?></b><br><span style="color:var(--muted);font-size:.88rem"><?= e($desc) ?></span></span>
      <?php if ($cur===$k): ?><span style="margin-left:auto;color:var(--green)">● active</span><?php endif; ?>
    </a>
  <?php endforeach; ?>
  </div>
  <p style="margin-top:1.2rem"><a href="/dashboard.php">← Back to apps</a></p>
</div>
<?php foot();
