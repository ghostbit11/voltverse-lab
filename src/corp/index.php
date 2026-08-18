<?php
require_once __DIR__ . '/../inc/site_layout.php';
$cfg = ['brand'=>'VoltCorp','ico'=>'🌐','accent'=>'#0d9488','home'=>'/corp/',
        'nav'=>[['About','/corp/page.php?page=about'],['Careers','/corp/page.php?page=careers'],['Privacy','/corp/page.php?page=privacy'],['Contact','#contact']],
        'cta'=>'Contact us','cta_href'=>'#contact'];
site_head('VoltCorp — Powering the future', $cfg);
$sub = $_COOKIE['corp_sub'] ?? 'you@voltcorp.local';
?>
<div class="hero">
  <div class="eyebrow">VoltCorp Inc.</div>
  <h1>Powering the future of technology</h1>
  <p>We build the gadgets, cloud and AI that move the world forward. Trusted by 40+ million customers.</p>
  <div style="margin-top:1.2rem"><a class="btn" href="/corp/page.php?page=about">About us</a>
    <a class="btn ghost" href="/corp/page.php?page=careers">We're hiring →</a></div>
</div>

<div class="feats">
  <div class="feat"><div class="ic">🛡️</div><h3>Security first</h3><p>Enterprise-grade protection across every product.</p></div>
  <div class="feat"><div class="ic">☁️</div><h3>Global cloud</h3><p>Low-latency infrastructure in 30+ regions.</p></div>
  <div class="feat"><div class="ic">🤖</div><h3>AI everywhere</h3><p>Intelligent assistants built into every app.</p></div>
</div>

<div class="card" id="contact">
  <h2 style="margin-top:0">📩 Newsletter subscription</h2>
  <p style="color:#64748b">Your notifications are currently sent to: <b><?= e($sub) ?></b></p>
  <!-- A05/A07 · CSRF: state change via GET with NO anti-CSRF token -->
  <form action="/corp/subscribe.php" method="get" style="display:flex;gap:.6rem">
    <input name="email" placeholder="new email…" value="<?= e($sub) ?>" style="flex:1;padding:.7rem .9rem;border:1px solid #d7deea;border-radius:10px">
    <button class="btn">Update email</button>
  </form>
  <p style="color:#94a3b8;font-size:.82rem;margin:.6rem 0 0">We'll only use your email to send product news. Unsubscribe anytime.</p>
</div>
<?php site_foot('VoltCorp');
