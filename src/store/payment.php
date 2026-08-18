<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$stu = $_COOKIE['st_user'] ?? 'customer@volt.local';
$secure = lvl_secure();
$cart = json_decode($_COOKIE['st_cart'] ?? '{}', true) ?: [];

// collect posted price/qty (carried from cart/checkout — client-controllable at Low/Med/High)
$lines = [];
foreach ($_POST as $k=>$v) if (preg_match('/^price_(\d+)$/',$k,$m)) {
    $pid=(int)$m[1]; $q=(int)($_POST["qty_$pid"] ?? 1);
    $stp=db()->prepare("SELECT * FROM products WHERE id=?"); $stp->execute([$pid]); $p=$stp->fetch(PDO::FETCH_ASSOC);
    if(!$p)continue;
    $price = $secure ? (float)$p['price'] : (float)$v;   // SECURE: server price; else trust client
    $lines[] = [$p['name'],$q,$price];
}
if (!$lines) { header('Location: /store/cart.php'); exit; }
$total = 0; foreach ($lines as [$n,$q,$pr]) $total += $q*$pr;

// STEP 2: card submitted → place order
if (isset($_POST['place'])) {
    db()->prepare("INSERT INTO orders(user_email,total,status) VALUES(?,?,?)")->execute([$stu,$total,'confirmed']);
    $oid = db()->lastInsertId();
    $ins = db()->prepare("INSERT INTO order_items(order_id,name,qty,price) VALUES(?,?,?,?)");
    foreach ($lines as [$n,$q,$pr]) $ins->execute([$oid,$n,$q,$pr]);
    setcookie('st_cart','{}',0,'/');
    store_head('Order placed');
    echo '<div class="section" style="max-width:640px;margin:2rem auto;text-align:center">
      <div style="font-size:3.4rem">✅</div><h1 style="font-weight:600">Order placed successfully!</h1>
      <p style="color:#878787">Order #'.(int)$oid.' · Paid <b>$'.number_format($total,2).'</b> · Arriving in 2 days</p>';
    if (!$secure && $total <= 0) echo '<div class="flag">🎉 You paid $'.number_format($total,2).' — price/quantity tampering exploited! Flag: VOLT{store_price_tampering}</div>';
    echo '<div style="margin-top:1rem"><a class="btn btn-blue" href="/store/account.php">View orders</a>
      <a class="btn btn-ghost" href="/store/">Continue shopping</a></div></div>';
    store_foot(); exit;
}

// STEP 1: show payment form
$hidden=''; foreach ($lines as $i=>[$n,$q,$pr]) { /* re-emit as posted keys for placement */ }
foreach ($_POST as $k=>$v) if (preg_match('/^(price|qty)_\d+$/',$k)) $hidden.='<input type="hidden" name="'.e($k).'" value="'.e($v).'">';
store_head('Payment');
?>
<div class="crumb"><a href="/store/cart.php">Cart</a> › Address › <b>Payment</b></div>
<div class="cartwrap">
  <form class="section" method="post">
    <h2 style="margin-top:0">Payment</h2>
    <div class="paycard">
      <div style="display:flex;justify-content:space-between"><span>VoltVerse Pay</span><span>VISA</span></div>
      <div class="num">4242 4242 4242 4242</div>
      <div style="display:flex;justify-content:space-between;font-size:.8rem"><span>ALEX CUSTOMER</span><span>12/28</span></div>
    </div>
    <div class="two" style="margin-top:1rem">
      <div class="field"><label>Card number</label><input name="card" value="4242 4242 4242 4242"></div>
      <div class="field"><label>Name on card</label><input name="cardname" value="Alex Customer"></div>
      <div class="field"><label>Expiry</label><input name="exp" value="12/28"></div>
      <div class="field"><label>CVV</label><input name="cvv" value="123" type="password"></div>
    </div>
    <?= $hidden ?>
    <button class="btn btn-buy" name="place" value="1" style="margin-top:.5rem">Pay $<?= number_format($total,2) ?> & Place order</button>
  </form>
  <div class="summary">
    <h3>Order summary</h3>
    <?php foreach ($lines as [$n,$q,$pr]): ?><div class="srow"><span><?= e($n) ?> × <?= $q ?></span><span>$<?= number_format($q*$pr,2) ?></span></div><?php endforeach; ?>
    <div class="srow total"><span>Payable</span><span>$<?= number_format($total,2) ?></span></div>
  </div>
</div>
<?php store_foot();
