<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
store_head('Online Shopping');
$rows = db()->query("SELECT * FROM products ORDER BY rating DESC")->fetchAll(PDO::FETCH_ASSOC);

function pcard($p){
  $mrp = mrp($p['price']); $off = off($p['price']); $rc = rcount($p['id']);
  echo '<div class="pcard badge-deal">
    <div class="pimg"><a href="/store/product.php?id='.(int)$p['id'].'">'.e($p['emoji']).'</a></div>
    <div class="ttl"><a href="/store/product.php?id='.(int)$p['id'].'">'.e($p['name']).'</a></div>
    <div class="rate"><span class="rbadge">'.e($p['rating']).' ★</span><span class="rcount">('.$rc.')</span></div>
    <div><span class="price">$'.number_format($p['price'],0).'</span><span class="mrp">$'.number_format($mrp,0).'</span><span class="off">'.$off.'% off</span></div>
    <div class="deliver">Free delivery</div>
    <a class="btn btn-cart full" href="/store/cart.php?add='.(int)$p['id'].'" style="margin-top:.6rem">Add to Cart</a></div>';
}
?>
<div class="promo">
  <div><h3>Big Volt Sale is live ⚡</h3><p>Up to 35% off on premium gadgets · Free delivery · No-cost EMI</p></div>
  <a class="btn btn-buy" href="/store/search.php?q=">Shop all deals →</a>
</div>
<h2 style="font-size:1.15rem;margin:.4rem 0 .9rem;font-weight:700">Featured products</h2>
<div class="pgrid">
<?php foreach ($rows as $p) pcard($p); ?>
</div>
<?php store_foot();
