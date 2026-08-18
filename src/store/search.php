<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$q = $_GET['q'] ?? '';
store_head('Search');
$rows = []; $err = null; $leak = false; $lvl = level();
$filtered = $q;
// medium & high: strip the keywords once (case-insensitive) → nest them so a copy survives the filter
if ($lvl === 'medium' || $lvl === 'high') $filtered = str_ireplace(['union','select'], '', $q);
if ($lvl === 'secure') {
    $st = db()->prepare("SELECT id,name,category,price,emoji,rating FROM products WHERE name LIKE ? OR category LIKE ?");
    $st->execute(["%$q%","%$q%"]); $rows = $st->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT id,name,category,price,emoji,rating FROM products WHERE name LIKE '%$filtered%' OR category LIKE '%$filtered%'";
    try { $rows = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) if (strpos(json_encode($r),'VOLT{')!==false) $leak=true;
    } catch (Throwable $ex) { $err = $ex->getMessage(); }
}
?>
<div class="crumb"><a href="/store/">Home</a> › Search results for “<?= e($q) ?>”</div>
<?php if ($err): ?><div class="notice">SQL error: <?= e($err) ?></div><?php endif; ?>
<?php if ($leak): ?><div class="flag">🚩 SQL injection succeeded — you extracted data from another table.</div><?php endif; ?>
<div class="pgrid">
<?php foreach ($rows as $p): $price=is_numeric($p['price'])?'$'.number_format($p['price'],0):e($p['price']); $rc=rcount((int)$p['id']); ?>
  <div class="pcard"><div class="pimg"><a href="/store/product.php?id=<?= (int)$p['id'] ?>"><?= e($p['emoji']) ?></a></div>
    <div class="ttl"><a href="/store/product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a></div>
    <div class="rate"><span class="rbadge"><?= e($p['rating']) ?> ★</span><span class="rcount">(<?= $rc ?>)</span></div>
    <div><span class="price"><?= $price ?></span></div>
    <a class="btn btn-cart full" href="/store/cart.php?add=<?= (int)$p['id'] ?>" style="margin-top:.6rem">Add to Cart</a></div>
<?php endforeach; ?>
<?php if (!$rows && !$err): ?><p>No products found for “<?= e($q) ?>”.</p><?php endif; ?>
</div>
<?php store_foot();
