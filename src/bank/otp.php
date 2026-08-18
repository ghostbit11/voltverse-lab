<?php
require_once __DIR__ . '/../inc/bank_layout.php';
$secure = lvl_secure();
$step = $_POST['step'] ?? 'start';
$msg = null; $flag = null; $showOtp = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 'verify') {
        $otp = trim($_POST['otp'] ?? '');
        // SECURE: only the real 6-digit OTP (sent to the user's device) is accepted.
        // LOW: broken verification — ANY 6-digit value passes.
        if ($secure) {
            $msg = ($otp === (string)($_SESSION['bank_otp'] ?? 'xxxxxx')) ? 'ok' : 'Invalid OTP. Try again.';
            if ($msg === 'ok') $msg = 'Transfer of $50,000 authorised ✅';
        } else {
            if (preg_match('/^\d{6}$/', $otp)) { $msg = 'Transfer of $50,000 authorised ✅'; $flag = 'VOLT{bank_otp_bypass}'; }
            else $msg = 'Enter a 6-digit code.';
        }
    }
}
bank_head('Wire transfer · OTP', 'transfer');
$_SESSION['bank_otp'] = '748193';   // "sent to device"
?>
<div style="max-width:520px">
  <div class="card">
    <h2 style="margin-top:0">🔐 Authorise wire transfer</h2>
    <p style="color:#5a6b82">A one-time code was sent to your registered device to authorise a <b>$50,000</b> transfer.</p>
    <?php if ($msg): ?><div class="<?= strpos($msg,'authorised')!==false?'flag':'warn' ?>"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($flag): ?><div class="flag">🚩 OTP verification bypassed — any 6-digit code was accepted! Flag: <?= e($flag) ?></div><?php endif; ?>
    <form method="post"><input type="hidden" name="step" value="verify">
      <div class="field"><label>Enter OTP</label><input name="otp" placeholder="6-digit code" maxlength="6"></div>
      <button class="btn btn-p full">Authorise transfer</button></form>
    <?php if (!$secure): ?><div class="warn" style="margin-top:1rem">The server never checks the code against the one it sent —
      it only checks the format. Enter any 6 digits (e.g. <code>000000</code>).</div><?php endif; ?>
  </div>
</div>
<?php bank_foot();
