<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$secure = lvl_secure();
$msg = $_POST['message'] ?? '';
$rendered = null; $ssti = false;
if ($msg !== '') {
    if ($secure) {
        $rendered = e($msg);   // SECURE: plain text, no template evaluation
    } else {
        // VULN: user input is evaluated as a template → SSTI (RCE)
        $rendered = preg_replace_callback('/\{\{(.+?)\}\}/', function($m){
            try { return (string) eval('return ' . $m[1] . ';'); } catch (Throwable $e) { return '[error]'; }
        }, $msg);
        if ($rendered !== $msg && preg_match('/\{\{.+\}\}/', $msg)) $ssti = true;
    }
}
store_head('Gift card');
?>
<div class="crumb"><a href="/store/">Home</a> › Gift card</div>
<div class="cartwrap">
  <div class="section">
    <h2 style="margin-top:0">🎁 Personalise a gift card</h2>
    <form method="post">
      <label>Card message (supports <code>{{ }}</code> template tags)</label>
      <textarea name="message" rows="3" placeholder="Happy birthday, {{ name }}!"><?= e($msg) ?></textarea>
      <div style="margin-top:.6rem"><button class="btn btn-cart">Preview card</button></div>
    </form>
    <p style="color:#878787;font-size:.82rem">Personalise your message with <code>{{ name }}</code> tags — they're filled in automatically.</p>
  </div>
  <div class="section">
    <h3 style="margin-top:0">Preview</h3>
    <div style="background:linear-gradient(135deg,#4f46e5,#22d3ee);color:#fff;border-radius:14px;padding:2rem;min-height:120px;font-size:1.15rem">
      <?= $rendered !== null ? $rendered : '<span style="opacity:.7">Your message appears here…</span>' ?>
    </div>
    <?php if ($ssti): ?><div class="flag">🚩 Server-Side Template Injection — your expression executed on the server! Flag: VOLT{store_ssti}</div><?php endif; ?>
  </div>
</div>
<?php store_foot();
