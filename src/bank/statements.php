<?php
require_once __DIR__ . '/../inc/bank_layout.php';
$me = 'customer@volt.local';
$myAcc = db()->query("SELECT * FROM accounts WHERE owner='" . $me . "'")->fetch(PDO::FETCH_ASSOC);
$acc = (int)($_GET['acc'] ?? $myAcc['id']);
if (lvl_secure()) $acc = (int)$myAcc['id'];              // SECURE: always your own
$a = db()->prepare("SELECT * FROM accounts WHERE id=?"); $a->execute([$acc]); $a=$a->fetch(PDO::FETCH_ASSOC);
bank_head('Account statement', 'statements');
if (!$a) { echo '<div class="card">Account not found.</div>'; bank_foot(); exit; }
$idor = $a['owner'] !== $me;
$tx = db()->prepare("SELECT * FROM txns WHERE account_id=? ORDER BY id DESC"); $tx->execute([$acc]);
$txns = $tx->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:1rem">
    <div><h2 style="margin:0"><?= e($a['holder']) ?></h2><div style="color:#8496ad;font-family:monospace"><?= e($a['number']) ?></div></div>
    <form method="get" style="display:flex;gap:.5rem;align-items:end"><div class="field" style="margin:0"><label>Statement for account id</label><input name="acc" value="<?= (int)$acc ?>"></div><button class="btn btn-g">Load</button></form>
  </div>
  <?php if ($idor): ?><div class="flag">👀 You are reading <b><?= e($a['holder']) ?></b>'s statement — IDOR! Flag: VOLT{bank_statement_idor}</div><?php endif; ?>
  <table style="margin-top:1rem"><tr><th>Date</th><th>Description</th><th style="text-align:right">Amount</th><th style="text-align:right">Balance</th></tr>
  <?php $run=$a['balance']; foreach ($txns as $t): $neg=$t['amount']<0; ?>
    <tr><td><?= e($t['ts']) ?></td><td><?= e($t['descr']) ?></td>
      <td style="text-align:right" class="<?= $neg?'debit':'credit' ?>"><?= ($neg?'−':'+').'$'.number_format(abs($t['amount']),2) ?></td>
      <td style="text-align:right">$<?= number_format($run,2) ?></td></tr>
  <?php $run-=$t['amount']; endforeach; ?>
  <?php if(!$txns): ?><tr><td colspan="4" style="color:#8496ad">No transactions.</td></tr><?php endif; ?></table>
</div>
<?php bank_foot();
