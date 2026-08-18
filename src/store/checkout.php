<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$cart = json_decode($_COOKIE['st_cart'] ?? '{}', true) ?: [];
if (!$cart) { header('Location: /store/cart.php'); exit; }
store_head('Checkout · Address');
$total=0; $hidden='';
foreach ($cart as $pid=>$qty){ $st=db()->prepare("SELECT price FROM products WHERE id=?"); $st->execute([(int)$pid]);
  $pr=$st->fetchColumn(); if($pr===false)continue; $total+=$pr*$qty;
  $hidden.='<input type="hidden" name="price_'.$pid.'" value="'.$pr.'"><input type="hidden" name="qty_'.$pid.'" value="'.(int)$qty.'">'; }
?>
<div class="crumb"><a href="/store/cart.php">Cart</a> › Delivery address › Payment</div>
<div class="cartwrap">
  <form class="section" action="/store/payment.php" method="post">
    <h2 style="margin-top:0">Delivery address</h2>
    <div class="two">
      <div class="field"><label>Full name</label><input name="name" value="Alex Customer" required></div>
      <div class="field"><label>Phone</label><input name="phone" value="+91 90000 00000" required></div>
    </div>
    <div class="field"><label>Address</label><textarea name="addr" rows="2" required>221B Volt Street, Tech Park</textarea></div>
    <div class="two">
      <div class="field"><label>City</label><input name="city" value="Bengaluru" required></div>
      <div class="field"><label>Pincode</label><input name="pin" value="560001" required></div>
    </div>
    <?= $hidden ?>
    <button class="btn btn-buy" style="margin-top:.5rem">Continue to payment →</button>
  </form>
  <div class="summary">
    <h3>Order summary</h3>
    <div class="srow"><span>Items total</span><span>$<?= number_format($total,0) ?></span></div>
    <div class="srow"><span>Delivery</span><span style="color:#388e3c">FREE</span></div>
    <div class="srow total"><span>Payable</span><span>$<?= number_format($total,0) ?></span></div>
  </div>
</div>
<?php store_foot();
