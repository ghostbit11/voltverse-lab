<?php
require_once __DIR__ . '/inc/layout.php';
require_login();
ensure_siem();
$events = db()->query("SELECT * FROM siem ORDER BY id DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
$total = (int)db()->query("SELECT COUNT(*) FROM siem")->fetchColumn();
$byType = db()->query("SELECT type,COUNT(*) c FROM siem GROUP BY type ORDER BY c DESC")->fetchAll(PDO::FETCH_ASSOC);
$actors = db()->query("SELECT actor,COUNT(*) c FROM siem GROUP BY actor ORDER BY c DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
head('SOC · Blue Team');
?>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">🛡️ Security Operations Center</span>
  <h1 style="font-size:2rem">Blue Team console</h1>
  <p>Every attack you run against the range is detected and logged here in real time — just like a real SIEM.
     Switch to the attacker side, fire some payloads, then come back and watch them light up.</p>
  <div class="stat">
    <div class="b"><b class="gradtext" style="color:#f87171!important"><?= $total ?></b><span>ALERTS</span></div>
    <div class="b"><b class="gradtext"><?= count($byType) ?></b><span>ATTACK TYPES</span></div>
    <div class="b"><b class="gradtext"><?= count($actors) ?></b><span>ACTORS</span></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:1.2rem;margin-top:1.4rem">
  <div class="panel" style="padding:0;overflow:hidden">
    <div style="padding:.9rem 1.2rem;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center">
      <h3 style="margin:0">🚨 Live alert stream</h3><span style="color:var(--muted);font-size:.78rem">auto-refresh 5s</span></div>
    <div style="max-height:460px;overflow:auto">
      <table style="width:100%;border-collapse:collapse;font-size:.85rem">
        <tr style="text-align:left;color:var(--muted);font-size:.68rem;text-transform:uppercase"><th style="padding:.6rem .9rem">Time</th><th>Type</th><th>Actor</th><th>Detail</th></tr>
        <?php foreach ($events as $e): ?>
          <tr style="border-top:1px solid var(--line)"><td style="padding:.55rem .9rem;color:var(--dim);white-space:nowrap"><?= e($e['ts']) ?></td>
            <td><span style="color:#fca5a5;font-weight:700;background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);border-radius:6px;padding:1px 8px;font-size:.72rem"><?= e($e['type']) ?></span></td>
            <td style="color:var(--muted)"><?= e($e['actor']) ?></td>
            <td style="color:var(--muted);font-family:ui-monospace,monospace;font-size:.78rem"><?= e($e['detail']) ?> <span style="color:var(--dim)"><?= e($e['uri']) ?></span></td></tr>
        <?php endforeach; ?>
        <?php if(!$events): ?><tr><td colspan="4" style="padding:1.4rem;text-align:center;color:var(--muted)">No alerts yet — go attack an app, then refresh. Try SQLi, XSS or a prompt injection.</td></tr><?php endif; ?>
      </table>
    </div>
  </div>
  <div>
    <div class="panel"><h3 style="margin-top:0">Alerts by type</h3>
      <?php foreach ($byType as $t): $w = $total? round(100*$t['c']/$total):0; ?>
        <div style="margin:.5rem 0"><div style="display:flex;justify-content:space-between;font-size:.85rem"><span><?= e($t['type']) ?></span><b><?= $t['c'] ?></b></div>
          <div style="height:6px;background:rgba(255,255,255,.07);border-radius:999px;overflow:hidden"><div style="height:100%;width:<?= $w ?>%;background:linear-gradient(90deg,#f87171,#fb923c)"></div></div></div>
      <?php endforeach; ?>
      <?php if(!$byType): ?><span style="color:var(--muted);font-size:.85rem">No data.</span><?php endif; ?>
    </div>
    <div class="panel"><h3 style="margin-top:0">Top actors</h3>
      <?php foreach ($actors as $a): ?><div style="display:flex;justify-content:space-between;font-size:.85rem;margin:.4rem 0"><span style="color:var(--muted)"><?= e($a['actor']) ?></span><b><?= $a['c'] ?></b></div><?php endforeach; ?>
    </div>
  </div>
</div>
<script>setTimeout(()=>location.reload(),5000);</script>
<?php foot();
