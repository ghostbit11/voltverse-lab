<?php
require_once __DIR__ . '/../inc/layout.php';
require_login();
$secure = lvl_secure();

// A legacy "preferences" object that apps serialize into a portable settings blob.
class VoltPrefs {
    public $theme = 'light';
    public $role  = 'user';
    public function __wakeup() {
        // VULN: object comes back to life via unserialize() and its magic method trusts its own fields.
        if (($this->role ?? '') === 'admin') { $GLOBALS['vv_pwn'] = true; }
    }
}

$flag = null; $msg = null; $obj = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blob'])) {
    $raw = base64_decode(trim($_POST['blob']), true);
    if ($raw === false) { $msg = 'Invalid preferences blob.'; }
    elseif ($secure) {
        // SECURE: never rebuild arbitrary classes from user input.
        $obj = unserialize($raw, ['allowed_classes' => false]);
        $msg = 'Preferences imported (safe mode — no class instantiation).';
    } else {
        $obj = @unserialize($raw);                       // VULN: object injection
        $msg = 'Preferences imported.';
        if (!empty($GLOBALS['vv_pwn'])) $flag = 'VOLT{php_object_injection}';
    }
}
$sample = base64_encode(serialize(new VoltPrefs()));      // a legitimate export (role=user)
$APP = ['ico'=>'🧩','name'=>'VoltSync (preferences)','lab'=>'VoltSync'];
head('VoltSync', $APP);
?>
<div style="margin-bottom:1rem"><a href="/dashboard.php">← Apps</a></div>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">🧩 VoltSync · portable settings</span>
  <h1 style="font-size:2rem">Import preferences</h1>
  <p>VoltSync lets you carry your theme &amp; account preferences between devices as a portable, base64-encoded blob.</p>
</div>
<div class="panel" style="margin-top:1.4rem;max-width:720px">
  <h3 style="margin-top:0">Your current export</h3>
  <textarea rows="2" readonly style="width:100%;background:rgba(255,255,255,.05);border:1px solid var(--line);color:#93c5fd;border-radius:10px;padding:.6rem;font-family:ui-monospace,monospace;font-size:.78rem"><?= e($sample) ?></textarea>
  <h3>Import a preferences blob</h3>
  <form method="post"><textarea name="blob" rows="3" placeholder="paste base64…" style="width:100%;background:rgba(255,255,255,.05);border:1px solid var(--line);color:var(--fg);border-radius:10px;padding:.6rem;font-family:ui-monospace,monospace;font-size:.78rem"></textarea>
    <button class="btn full" style="margin-top:.6rem">Import preferences</button></form>
  <?php if ($msg): ?><div class="note" style="margin-top:.8rem;color:var(--muted)"><?= e($msg) ?><?php if($obj && !$secure): ?> · theme=<b><?= e($obj->theme ?? '?') ?></b>, role=<b><?= e($obj->role ?? '?') ?></b><?php endif; ?></div><?php endif; ?>
  <?php if ($flag): ?><div style="background:rgba(52,211,153,.12);border:1px dashed var(--green);color:#a7f3d0;border-radius:10px;padding:.7rem;margin-top:.6rem;font-family:ui-monospace,monospace">🚩 A crafted object with elevated fields was deserialized — PHP object injection! Flag: VOLT{php_object_injection}</div><?php endif; ?>
</div>
<?php foot();
