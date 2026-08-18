<?php
require_once __DIR__ . '/../inc/bank_layout.php';
$me = 'customer@volt.local';
$secure = lvl_secure();
$myAcc = db()->query("SELECT * FROM accounts WHERE owner='" . $me . "'")->fetch(PDO::FETCH_ASSOC);
$done = false; $err = null; $flag = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from = (int)($_POST['from'] ?? 0); $to = (int)($_POST['to'] ?? 0); $amount = (float)($_POST['amount'] ?? 0);
    if ($secure) {
        if ($from !== (int)$myAcc['id']) $err = "You can only transfer from your own account.";
        elseif ($amount <= 0) $err = "Amount must be greater than zero.";
        elseif ($amount > $myAcc['balance']) $err = "Insufficient funds.";
    } else {
        if ($amount < 0) $flag = "VOLT{bank_negative_amount_logic}";              // negative → you receive money
        if ($from !== (int)$myAcc['id']) $flag = $flag ?? "VOLT{bank_transfer_from_any_account}"; // BOLA
    }
    if (!$err) {
        db()->prepare("UPDATE accounts SET balance=balance-? WHERE id=?")->execute([$amount, $from]);
        db()->prepare("UPDATE accounts SET balance=balance+? WHERE id=?")->execute([$amount, $to]);
        db()->prepare("INSERT INTO txns(account_id,descr,amount,ts) VALUES(?,?,?,?)")->execute([$from, "Transfer to #$to", -$amount, '2026-08-16']);
        $done = true;
    }
}
bank_head('Transfer money', 'transfer');
$myAcc = db()->query("SELECT * FROM accounts WHERE owner='" . $me . "'")->fetch(PDO::FETCH_ASSOC);
?>
<div style="max-width:520px">
<?php if ($done): ?>
  <div class="card"><div style="font-size:2.4rem">✅</div><h2 style="margin:.3rem 0">Transfer successful</h2>
    <p style="color:#5a6b82">Your updated balance: <b>$<?= number_format($myAcc['balance'],2) ?></b></p>
    <?php if ($flag): ?><div class="flag">🚩 Flaw exploited. Flag: <?= e($flag) ?></div><?php endif; ?>
    <a class="btn btn-p" href="/bank/">Back to dashboard</a></div>
<?php else: ?>
  <div class="card">
    <?php if ($err): ?><div class="warn"><?= e($err) ?></div><?php endif; ?>
    <form method="post">
      <div class="field"><label>From account</label><input name="from" value="<?= (int)$myAcc['id'] ?>"></div>
      <div class="field"><label>To account (beneficiary id)</label><input name="to" value="5002"></div>
      <div class="field"><label>Amount ($)</label><input name="amount" value="100"></div>
      <button class="btn btn-p full">Send money</button>
    </form>
  </div>
<?php endif; ?>
</div>
<?php bank_foot();
