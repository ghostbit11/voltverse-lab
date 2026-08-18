<?php
require_once __DIR__ . '/../inc/bank_layout.php';
$me = 'customer@volt.local';
$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare("SELECT * FROM accounts WHERE id=?"); $st->execute([$id]);
$a = $st->fetch(PDO::FETCH_ASSOC);
bank_head('Account details', 'dashboard');
if (!$a) { echo '<div class="card">Account not found. <a href="/bank/">← Back</a></div>'; bank_foot(); exit; }
// A01/BOLA: at Low/Med/High any account id works; Secure enforces ownership
if (lvl_secure() && $a['owner'] !== $me) { echo '<div class="card"><h2>403 — not your account.</h2><a href="/bank/">← Back</a></div>'; bank_foot(); exit; }
$bola = $a['owner'] !== $me;
$tx = db()->prepare("SELECT * FROM txns WHERE account_id=? ORDER BY id DESC"); $tx->execute([$id]);
$txns = $tx->fetchAll(PDO::FETCH_ASSOC);
?>
<a href="/bank/">← Dashboard</a>
<div class="card balcard" style="margin-top:.8rem">
  <div class="lab"><?= e($a['holder']) ?> · <?= e($a['owner']) ?></div>
  <div class="amt">$<?= number_format($a['balance'],2) ?></div>
  <div class="num"><?= e($a['number']) ?></div>
</div>
<?php if ($bola): ?><div class="flag">👀 You accessed <b><?= e($a['holder']) ?></b>'s account — Broken Object Level Authorization (BOLA/IDOR)!<?php if ($a['secret']): ?> Flag: <?= e($a['secret']) ?><?php endif; ?></div><?php endif; ?>
<div class="card">
  <h2 style="margin-top:0">Transactions</h2>
  <table><tr><th>Date</th><th>Description</th><th style="text-align:right">Amount</th></tr>
  <?php foreach ($txns as $t): $neg=$t['amount']<0; ?>
    <tr><td><?= e($t['ts']) ?></td><td><?= e($t['descr']) ?></td><td style="text-align:right" class="<?= $neg?'debit':'credit' ?>"><?= ($neg?'−':'+').'$'.number_format(abs($t['amount']),2) ?></td></tr>
  <?php endforeach; ?>
  <?php if(!$txns): ?><tr><td colspan="3" style="color:#8496ad">No transactions.</td></tr><?php endif; ?></table>
</div>
<?php bank_foot();
