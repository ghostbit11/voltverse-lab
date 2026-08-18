<?php
require_once __DIR__ . '/../inc/bank_layout.php';
$me = 'customer@volt.local';
$acc = db()->query("SELECT * FROM accounts WHERE owner='" . $me . "'")->fetch(PDO::FETCH_ASSOC);
bank_head('Dashboard', 'dashboard');
$tx = db()->prepare("SELECT * FROM txns WHERE account_id=? ORDER BY id DESC LIMIT 6"); $tx->execute([$acc['id']]);
$txns = $tx->fetchAll(PDO::FETCH_ASSOC);
$all = db()->query("SELECT * FROM accounts")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="cards">
  <div class="card balcard">
    <div class="lab">Savings Account · <?= e($acc['holder']) ?></div>
    <div class="amt">$<?= number_format($acc['balance'],2) ?></div>
    <div class="num"><?= e($acc['number']) ?></div>
  </div>
  <div class="card"><div style="color:#8496ad;font-size:.8rem">Available credit</div><div style="font-size:1.6rem;font-weight:800">$12,000</div><div style="color:#137333;font-size:.82rem">Aurora Platinum Card</div></div>
  <div class="card"><div style="color:#8496ad;font-size:.8rem">Rewards</div><div style="font-size:1.6rem;font-weight:800">4,820 pts</div><div style="color:#8496ad;font-size:.82rem">≈ $48 cashback</div></div>
</div>

<div class="qa">
  <a href="/bank/transfer.php"><span class="ic">💸</span>Transfer</a>
  <a href="/bank/otp.php"><span class="ic">🏦</span>Wire ($50k)</a>
  <a href="/bank/statements.php"><span class="ic">📄</span>Statements</a>
  <a href="/bank/cards.php"><span class="ic">💳</span>Cards</a>
  <a href="/bank/redeem.php"><span class="ic">🎁</span>Rewards</a>
</div>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center"><h2 style="margin:0">Recent transactions</h2><a href="/bank/statements.php">View all →</a></div>
  <table><tr><th>Date</th><th>Description</th><th style="text-align:right">Amount</th></tr>
  <?php foreach ($txns as $t): $neg=$t['amount']<0; ?>
    <tr><td><?= e($t['ts']) ?></td><td><?= e($t['descr']) ?></td>
      <td style="text-align:right" class="<?= $neg?'debit':'credit' ?>"><?= ($neg?'−':'+').'$'.number_format(abs($t['amount']),2) ?></td></tr>
  <?php endforeach; ?></table>
</div>

<div class="card">
  <h2 style="margin-top:0">Your accounts & beneficiaries</h2>
  <table><tr><th>Holder</th><th>Account no.</th><th style="text-align:right">Action</th></tr>
  <?php foreach ($all as $a): ?>
    <tr><td><?= e($a['holder']) ?></td><td style="font-family:monospace"><?= e($a['number']) ?></td>
      <td style="text-align:right"><a href="/bank/account.php?id=<?= (int)$a['id'] ?>">View</a></td></tr>
  <?php endforeach; ?></table>
  <div class="warn" style="margin-top:.8rem">Tip: account statements are addressed by account id — <code>/bank/account.php?id=5001</code></div>
</div>
<?php bank_foot();
