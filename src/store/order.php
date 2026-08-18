<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$stu = $_COOKIE['st_user'] ?? 'customer@volt.local';
$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare("SELECT * FROM orders WHERE id=?"); $st->execute([$id]);
$o = $st->fetch(PDO::FETCH_ASSOC);
store_head("Order #$id");
if (!$o) { echo '<div class="section">Order not found.</div>'; store_foot(); exit; }
if (lvl_secure() && $o['user_email'] !== $stu) {
    echo '<div class="section"><h2>403 — this order isn\'t yours.</h2><a href="/store/account.php">← Back</a></div>'; store_foot(); exit;
}
$it = db()->prepare("SELECT * FROM order_items WHERE order_id=?"); $it->execute([$id]);
$items = $it->fetchAll(PDO::FETCH_ASSOC);
$idor = $o['user_email'] !== $stu;
?>
<div class="crumb"><a href="/store/account.php">My orders</a> › Order #<?= (int)$id ?></div>
<div class="section">
  <h2 style="margin-top:0">Order #<?= (int)$id ?></h2>
  <?php if ($idor): ?><div class="flag">👀 Viewing an order owned by <b><?= e($o['user_email']) ?></b> — IDOR! Check the status note below.</div><?php endif; ?>
  <p style="color:#878787">Customer: <b><?= e($o['user_email']) ?></b> · Status: <?= e($o['status']) ?></p>
  <table style="width:100%"><tr style="text-align:left;color:#878787;font-size:.8rem"><th style="padding:.5rem">Item</th><th>Qty</th><th>Price</th></tr>
  <?php foreach ($items as $i): ?><tr style="border-top:1px solid #f0f0f0"><td style="padding:.6rem .5rem"><?= e($i['name']) ?></td><td><?= (int)$i['qty'] ?></td><td>$<?= number_format($i['price'],2) ?></td></tr><?php endforeach; ?></table>
  <p style="text-align:right;font-size:1.1rem"><b>Total: $<?= number_format($o['total'],2) ?></b></p>
</div>
<?php store_foot();
