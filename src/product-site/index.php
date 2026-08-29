<?php
require_once __DIR__ . '/../inc/site_layout.php';
$cfg = ['brand'=>'VoltBook Pro','ico'=>'📦','accent'=>'#0ea5e9','lab'=>'VoltBook Microsite','home'=>'/product-site/',
        'nav'=>[['Features','#feat'],['Reviews','#rev'],['Deals','#deals']],'cta'=>'Pre-order','cta_href'=>'#deals'];
site_head('The all-new VoltBook Pro', $cfg);
$secure = lvl_secure();
$q   = $_GET['q'] ?? '';
$ref = $_GET['ref'] ?? '';
// A03 · Reflected XSS — the reflection is filtered differently per difficulty level.
$lvl = level();
$filter = function($s) use ($secure,$lvl) {
    if ($secure) return e($s);                                   // SECURE: fully HTML-escaped
    if ($lvl === 'medium') return str_ireplace('<script', '', $s);           // strips <script> → use <img onerror>
    if ($lvl === 'high')   return str_ireplace(['<script','onerror'], '', $s); // also strips onerror → use <svg onload>
    return $s;                                                   // LOW: raw
};
$showQ   = $filter($q);
$showRef = $filter($ref);
// flag fires only if a live vector actually survived into the reflected output
$xss = (!$secure && preg_match('/<script|onerror\s*=|onload\s*=/i', $showQ.$showRef));
?>
<div class="hero">
  <div class="eyebrow">New · 2026 edition</div>
  <h1>The all-new <span style="color:var(--acc)">VoltBook Pro</span></h1>
  <p>Featherlight power. 18-hour battery. The most advanced Volt laptop ever built.</p>
  <?php if ($ref!==''): ?><p style="font-weight:600">👋 Welcome, <?= $showRef ?>! Here's an exclusive offer for you.</p><?php endif; ?>
  <div style="margin-top:1.2rem"><a class="btn" href="#deals">Pre-order now →</a>
    <a class="btn ghost" href="/store/product.php?id=2">View in store</a></div>
</div>

<div class="card">
  <form method="get" style="display:flex;gap:.6rem">
    <input name="q" placeholder="Search offers & accessories…" value="<?= e($q) ?>" style="flex:1;padding:.7rem .9rem;border:1px solid #d7deea;border-radius:10px">
    <button class="btn">Search</button>
  </form>
  <?php if ($q!==''): ?><p style="margin-top:.8rem">Showing results for: <b><?= $showQ ?></b></p><?php endif; ?>
  <?php if ($xss): ?><div class="flag">🚩 Reflected XSS — your script ran in the page! Flag: VOLT{microsite_reflected_xss}</div><?php endif; ?>
</div>

<h2 id="feat">Why VoltBook Pro</h2>
<div class="feats">
  <div class="feat"><div class="ic">⚡</div><h3>VoltSilicon M3</h3><p>Up to 2× faster than the previous generation.</p></div>
  <div class="feat"><div class="ic">🔋</div><h3>18-hour battery</h3><p>All-day power on a single charge.</p></div>
  <div class="feat"><div class="ic">🖥️</div><h3>Liquid Retina</h3><p>Stunning 14-inch display, 1000 nits.</p></div>
</div>

<div class="card" id="deals">
  <h2 style="margin-top:0">🔥 Partner deals</h2>
  <p style="color:#64748b">We've teamed up with partners for exclusive launch offers. Continue to a partner to claim yours:</p>
  <a class="btn" href="/product-site/go.php?url=https://partner-offers.example/voltbook">Claim partner offer →</a>
  <form action="/product-site/go.php" method="get" style="display:flex;gap:.6rem;margin-top:1rem">
    <input name="url" value="/store/" placeholder="redirect url…" style="flex:1;padding:.6rem .9rem;border:1px solid #d7deea;border-radius:10px">
    <button class="btn ghost">Continue</button>
  </form>
</div>

<h2 id="rev">What people say</h2>
<div class="feats">
  <div class="feat"><p>"Best laptop I've owned."</p><b>— Priya</b></div>
  <div class="feat"><p>"Insane battery life."</p><b>— Sam</b></div>
  <div class="feat"><p>"Worth every penny."</p><b>— Jordan</b></div>
</div>
<?php site_foot('VoltBook Pro');
