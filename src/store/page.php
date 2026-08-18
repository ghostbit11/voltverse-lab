<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
// A03/A05 · Local File Inclusion / Path Traversal
$pages = ['about'=>'About VoltVerse','shipping'=>'Shipping & Delivery','returns'=>'Returns & Refunds','terms'=>'Terms of Service'];
$content = ['about'=>'VoltVerse is a premium electronics retailer serving 40+ countries.',
            'shipping'=>'Free delivery over $50. Orders ship within 24 hours.',
            'returns'=>'30-day easy returns on all products.','terms'=>'Use of this site is subject to our terms.'];
$p = $_GET['page'] ?? 'about';
store_head('Info');
$body = null; $err = null;
if (lvl_secure()) {
    $body = $content[$p] ?? 'Page not found.';   // SECURE: allow-listed keys only
} else {
    // VULN: builds a file path from user input → path traversal / LFI
    $file = __DIR__ . "/content/$p.txt";
    if (isset($content[$p])) $body = $content[$p];
    else { $raw = @file_get_contents($file); $body = $raw !== false ? $raw : ($err = "Could not read: $file"); }
}
?>
<div class="crumb"><a href="/store/">Home</a> › Info</div>
<div class="cartwrap">
  <div class="section">
    <h2 style="margin-top:0"><?= e($pages[$p] ?? $p) ?></h2>
    <?php if ($err): ?><div class="notice"><?= e($err) ?></div><?php endif; ?>
    <pre style="white-space:pre-wrap;font-family:inherit;color:#3e4152"><?= e($body) ?></pre>
    <?php if (!lvl_secure() && strpos((string)$body,'VOLT{')!==false): ?><div class="flag">🚩 Path traversal / LFI — you read a file outside the web root!</div><?php endif; ?>
  </div>
  <div class="summary"><h3>Help topics</h3>
    <?php foreach ($pages as $k=>$t): ?><div class="srow"><a href="/store/page.php?page=<?= e($k) ?>"><?= e($t) ?></a></div><?php endforeach; ?>
    <p style="color:#878787;font-size:.8rem;margin-top:.6rem">Try <code>?page=../../../../secret_lfi</code></p>
  </div>
</div>
<?php store_foot();
