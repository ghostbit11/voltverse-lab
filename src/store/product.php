<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    db()->prepare("INSERT INTO reviews(product_id,author,body) VALUES(?,?,?)")
        ->execute([$id, $_POST['author'] ?? 'Guest', $_POST['body'] ?? '']);
    header("Location: /store/product.php?id=$id#reviews"); exit;
}
$st = db()->prepare("SELECT * FROM products WHERE id=?"); $st->execute([$id]);
$p = $st->fetch(PDO::FETCH_ASSOC);
store_head($p ? $p['name'] : 'Product');
if (!$p) { echo '<div class="section">Product not found.</div>'; store_foot(); exit; }
$rs = db()->prepare("SELECT * FROM reviews WHERE product_id=? ORDER BY id DESC"); $rs->execute([$id]);
$reviews = $rs->fetchAll(PDO::FETCH_ASSOC);
$secure = lvl_secure();
$xss = false; foreach ($reviews as $r) if (!$secure && preg_match('/<script|onerror=/i',$r['body'])) $xss = true;
$mrp = mrp($p['price']); $off = off($p['price']); $rc = rcount($p['id']);
?>
<div class="crumb"><a href="/store/">Home</a> › <a href="/store/search.php?q=<?= urlencode($p['category']) ?>"><?= e($p['category']) ?></a> › <?= e($p['name']) ?></div>
<div class="pdp">
  <div class="gallery">
    <div class="mainimg"><?= e($p['emoji']) ?></div>
    <div class="buys">
      <a class="btn btn-cart full" href="/store/cart.php?add=<?= (int)$p['id'] ?>">🛒 Add to Cart</a>
      <a class="btn btn-buy full" href="/store/cart.php?add=<?= (int)$p['id'] ?>&buy=1">⚡ Buy Now</a>
    </div>
  </div>
  <div>
    <div style="color:#878787;font-size:.85rem"><?= e($p['category']) ?></div>
    <h1><?= e($p['name']) ?></h1>
    <div class="rate"><span class="rbadge"><?= e($p['rating']) ?> ★</span><span class="rcount"><?= $rc ?> ratings & <?= (int)($rc/6) ?> reviews</span></div>
    <div class="pblock">
      <span class="now">$<?= number_format($p['price'],0) ?></span>
      <span class="mrp" style="font-size:1rem">$<?= number_format($mrp,0) ?></span>
      <span class="off" style="font-size:1rem"><?= $off ?>% off</span>
    </div>
    <div class="deliver">✓ In stock · Free delivery by tomorrow</div>
    <div class="section" style="margin-top:.8rem;padding:1rem">
      <b>Available offers</b>
      <ul class="offers">
        <li>💳 <b>Bank Offer</b> 10% off on VoltVerse Bank cards, up to $50</li>
        <li>🏷️ <b>No-cost EMI</b> on orders above $300</li>
        <li>🎁 <b>Special Price</b> Get extra <?= $off ?>% off (price inclusive)</li>
      </ul>
    </div>
    <div class="section" style="margin-top:.8rem;padding:1rem">
      <b>About this item</b>
      <p style="color:#3e4152;margin:.5rem 0"><?= e($p['descr']) ?></p>
      <table class="specs">
        <tr><td>Category</td><td><?= e($p['category']) ?></td></tr>
        <tr><td>Rating</td><td><?= e($p['rating']) ?> / 5</td></tr>
        <tr><td>Warranty</td><td>2 years VoltCorp warranty</td></tr>
        <tr><td>In the box</td><td><?= e($p['name']) ?>, USB-C cable, manual</td></tr>
      </table>
    </div>
  </div>
</div>

<div class="section" id="reviews">
  <h2 style="margin-top:0">Ratings & Reviews</h2>
  <?php if ($xss): ?><div class="flag">🚩 Stored XSS executed in a viewer's browser. Flag: VOLT{store_stored_xss}</div><?php endif; ?>
  <?php foreach ($reviews as $r): ?>
    <div style="border-bottom:1px solid #f0f0f0;padding:.7rem 0">
      <span class="rbadge">5 ★</span> <b><?= e($r['author']) ?></b>
      <div style="color:#3e4152;margin-top:.3rem"><?= $secure ? e($r['body']) : $r['body'] ?></div></div>
  <?php endforeach; ?>
  <?php if (!$reviews): ?><p style="color:#878787">No reviews yet. Be the first!</p><?php endif; ?>
  <h3>Write a review</h3>
  <form method="post" class="two" style="align-items:end">
    <div class="field"><label>Your name</label><input type="text" name="author" value="Guest"></div>
    <div class="field" style="grid-column:1/3"><label>Review</label><textarea name="body" rows="2"></textarea></div>
    <div><button class="btn btn-blue" type="submit">Submit</button></div>
  </form>
</div>
<?php store_foot();
