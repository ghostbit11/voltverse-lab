<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$cart = json_decode($_COOKIE['st_cart'] ?? '{}', true) ?: [];
$save = function($c){ setcookie('st_cart', json_encode($c), 0, '/'); };
if (isset($_GET['add'])) { $k=(string)(int)$_GET['add']; $cart[$k]=($cart[$k]??0)+1; $save($cart);
    header('Location: ' . (isset($_GET['buy']) ? '/store/checkout.php' : '/store/cart.php')); exit; }
if (isset($_GET['inc'])) { $k=(string)(int)$_GET['inc']; $cart[$k]=($cart[$k]??0)+1; $save($cart); header('Location: /store/cart.php'); exit; }
if (isset($_GET['dec'])) { $k=(string)(int)$_GET['dec']; $cart[$k]=max(0,($cart[$k]??0)-1); if(!$cart[$k])unset($cart[$k]); $save($cart); header('Location: /store/cart.php'); exit; }
if (isset($_GET['rm']))  { unset($cart[(string)(int)$_GET['rm']]); $save($cart); header('Location: /store/cart.php'); exit; }
if (isset($_POST['coupon'])) { setcookie('st_coupon', $_POST['coupon'], 0, '/'); header('Location: /store/cart.php'); exit; }

// A04 · Business logic: coupon discount not capped to cart value
$coupon = $_COOKIE['st_coupon'] ?? '';
$coupons = ['SAVE10'=>10, 'VOLT50'=>50, 'WELCOME5000'=>5000];   // WELCOME5000 is way more than any cart
$discount = lvl_secure() ? 0 : ($coupons[strtoupper($coupon)] ?? 0);

store_head('Cart');
$total=0; $count=0; $items=[];
foreach ($cart as $pid=>$qty){ $st=db()->prepare("SELECT * FROM products WHERE id=?"); $st->execute([(int)$pid]);
  $p=$st->fetch(PDO::FETCH_ASSOC); if(!$p)continue; $line=$p['price']*$qty; $total+=$line; $count+=$qty; $items[]=[$p,$qty,$line]; }
?>
<div class="crumb"><a href="/store/">Home</a> › Cart</div>
<?php if (!$items): ?>
  <div class="section" style="text-align:center;padding:3rem">
    <div style="font-size:3rem">🛒</div><h2>Your cart is empty</h2>
    <a class="btn btn-buy" href="/store/">Shop now</a></div>
<?php else: ?>
<div class="cartwrap">
  <div>
  <?php foreach ($items as [$p,$qty,$line]): ?>
    <div class="cartitem">
      <div class="ci"><?= e($p['emoji']) ?></div>
      <div style="flex:1">
        <div style="font-weight:500"><a href="/store/product.php?id=<?= (int)$p['id'] ?>" style="color:#212121"><?= e($p['name']) ?></a></div>
        <div style="color:#878787;font-size:.85rem"><?= e($p['category']) ?> · Free delivery</div>
        <div style="margin-top:.6rem;display:flex;align-items:center;gap:1rem">
          <span class="qty"><a href="/store/cart.php?dec=<?= (int)$p['id'] ?>">−</a><span><?= $qty ?></span><a href="/store/cart.php?inc=<?= (int)$p['id'] ?>">+</a></span>
          <a href="/store/cart.php?rm=<?= (int)$p['id'] ?>" style="color:#878787;font-weight:600">REMOVE</a>
        </div>
      </div>
      <div style="text-align:right"><div class="price">$<?= number_format($line,0) ?></div>
        <div class="mrp">$<?= number_format(mrp($p['price'])*$qty,0) ?></div></div>
    </div>
  <?php endforeach; ?>
  </div>
  <div class="summary">
    <h3>Price details</h3>
    <div class="srow"><span>Price (<?= $count ?> items)</span><span>$<?= number_format($total*1.35,0) ?></span></div>
    <div class="srow"><span>Product discount</span><span style="color:#388e3c">− $<?= number_format($total*0.35,0) ?></span></div>
    <?php $payable = $total - $discount; if ($discount): ?>
      <div class="srow"><span>Coupon <?= e(strtoupper($coupon)) ?></span><span style="color:#388e3c">− $<?= number_format($discount,0) ?></span></div>
    <?php endif; ?>
    <div class="srow"><span>Delivery</span><span style="color:#388e3c">FREE</span></div>
    <div class="srow total"><span>Total Amount</span><span>$<?= number_format($payable,0) ?></span></div>
    <?php if (!lvl_secure() && $payable < 0): ?><div class="flag" style="font-size:.78rem">🚩 Coupon exceeds cart value — business logic flaw! Flag: VOLT{store_coupon_logic}</div><?php endif; ?>
    <form method="post" style="display:flex;gap:.4rem;margin:.7rem 0">
      <input name="coupon" placeholder="Coupon code" value="<?= e($coupon) ?>" style="padding:.45rem .6rem">
      <button class="btn btn-ghost" style="padding:.45rem .8rem">Apply</button></form>
    <a class="btn btn-buy full" href="/store/checkout.php">Place Order →</a>
  </div>
</div>
<?php endif; store_foot();
