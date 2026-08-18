<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$secure = lvl_secure();
$xml = $_POST['xml'] ?? '';
$items = []; $err = null; $xxe = false;
if ($xml !== '') {
    if ($secure) {
        // SECURE: reject DTDs / external entities entirely
        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $xml)) { $err = "DTDs and external entities are not allowed."; }
        else { $doc = @simplexml_load_string($xml); if ($doc) foreach ($doc->item as $it) $items[] = (string)$it; }
    } else {
        // VULN: a vulnerable parser resolves SYSTEM entities → XXE (file disclosure)
        $resolved = $xml;
        if (preg_match('/<!ENTITY\s+(\w+)\s+SYSTEM\s+["\']file:\/\/(.+?)["\']/i', $xml, $mm)) {
            $content = @file_get_contents($mm[2]);
            if ($content !== false) $resolved = str_replace('&'.$mm[1].';', $content, $xml);
        }
        $resolved = preg_replace('/<!DOCTYPE.*?\]>/s', '', $resolved);
        $doc = @simplexml_load_string($resolved);
        if ($doc) foreach ($doc->item as $it) $items[] = (string)$it; else $err = "Invalid XML.";
        foreach ($items as $v) if (strpos($v,'VOLT{')!==false) $xxe = true;
    }
}
store_head('Import wishlist');
$sample = "<?xml version=\"1.0\"?>\n<!DOCTYPE wishlist [<!ENTITY xxe SYSTEM \"file:///var/secret_lfi.txt\">]>\n<wishlist><item>&xxe;</item></wishlist>";
?>
<div class="crumb"><a href="/store/">Home</a> › Import wishlist</div>
<div class="section" style="max-width:720px">
  <h2 style="margin-top:0">📥 Import wishlist (XML)</h2>
  <p style="color:#64748b">Paste an exported wishlist XML to import your items.</p>
  <form method="post"><textarea name="xml" rows="6" style="font-family:ui-monospace,monospace;font-size:.82rem"><?= e($xml ?: $sample) ?></textarea>
    <div style="margin-top:.6rem"><button class="btn btn-cart">Import</button></div></form>
  <?php if ($err): ?><div class="notice" style="margin-top:1rem"><?= e($err) ?></div><?php endif; ?>
  <?php if ($items): ?><h3>Imported items</h3><ul><?php foreach ($items as $i) echo '<li>'.e($i).'</li>'; ?></ul><?php endif; ?>
  <?php if ($xxe): ?><div class="flag">🚩 XXE — the parser read a local file via an external entity! Flag: VOLT{store_xxe}</div><?php endif; ?>
</div>
<?php store_foot();
