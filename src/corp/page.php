<?php
require_once __DIR__ . '/../inc/site_layout.php';
$cfg = ['brand'=>'VoltCorp','ico'=>'🌐','accent'=>'#0d9488','home'=>'/corp/',
        'nav'=>[['About','/corp/page.php?page=about'],['Careers','/corp/page.php?page=careers'],['Privacy','/corp/page.php?page=privacy'],['Contact','/corp/#contact']],
        'cta'=>'Contact us','cta_href'=>'/corp/#contact'];
$content = [
  'about'   => "About VoltCorp\n\nFounded in 2016, VoltCorp designs premium consumer electronics, cloud services and AI assistants. We employ 12,000 people across 18 countries.",
  'careers' => "Careers at VoltCorp\n\nWe're hiring engineers, designers and security researchers. Great pay, remote-friendly, and a mission that matters.",
  'privacy' => "Privacy Policy\n\nWe respect your data. This page explains what we collect and how we use it.",
];
$p = $_GET['page'] ?? 'about';
site_head(ucfirst($p), $cfg);
$secure = lvl_secure();
$body = null; $err = null;
if ($secure) {
    $body = $content[$p] ?? 'Page not found.';                 // SECURE: allow-listed keys only
} else if (isset($content[$p])) {
    $body = $content[$p];
} else {
    // A03/A05 · Local File Inclusion / Path Traversal
    $file = __DIR__ . "/pages/$p.txt";
    $raw = @file_get_contents($file);
    $body = $raw !== false ? $raw : ($err = "Could not read page: $file");
}
?>
<div style="padding:2rem 0">
  <p style="color:#64748b"><a href="/corp/">Home</a> › <?= e($p) ?></p>
  <div class="card">
    <?php if ($err): ?><div class="notice"><?= e($err) ?></div><?php endif; ?>
    <pre style="background:#f8fafc;color:#0f172a;border:1px solid #eef1f6"><?= e($body) ?></pre>
    <?php if (!$secure && strpos((string)$body,'VOLT{')!==false): ?><div class="flag">🚩 Path traversal / LFI — you read a file outside the web root!</div><?php endif; ?>
  </div>
  <p style="color:#94a3b8;font-size:.85rem">Pages are loaded from a file by the <code>page</code> parameter.
    Try <code>?page=../../../../corp_secret</code></p>
</div>
<?php site_foot('VoltCorp');
