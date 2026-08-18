<?php
require_once __DIR__ . '/../inc/bank_layout.php';
$me = 'customer@volt.local';
bank_head('My cards', 'cards');
$secure = lvl_secure();
$full = '4929 8412 7734 5002'; $cvv = '481';
// A02 · Sensitive data exposure: full PAN + CVV returned in plaintext at Low/Med/High
$showNum = $secure ? '•••• •••• •••• 5002' : $full;
$showCvv = $secure ? '•••' : $cvv;
?>
<div class="chip">
  <div style="display:flex;justify-content:space-between"><span>Aurora Platinum</span><span>VISA</span></div>
  <div class="no"><?= e($showNum) ?></div>
  <div style="display:flex;justify-content:space-between;font-size:.8rem"><span>ALEX CUSTOMER</span><span>EXP 12/28</span><span>CVV <?= e($showCvv) ?></span></div>
</div>
<div class="card" style="margin-top:1rem;max-width:520px">
  <h2 style="margin-top:0">Card details</h2>
  <table>
    <tr><td style="color:#8496ad">Card number</td><td style="font-family:monospace"><?= e($showNum) ?></td></tr>
    <tr><td style="color:#8496ad">CVV</td><td style="font-family:monospace"><?= e($showCvv) ?></td></tr>
    <tr><td style="color:#8496ad">Expiry</td><td>12/28</td></tr>
    <tr><td style="color:#8496ad">Limit</td><td>$12,000</td></tr>
  </table>
  <?php if (!$secure): ?><div class="flag">🚩 Full card number & CVV exposed in plaintext — sensitive data exposure (A02)! Flag: VOLT{bank_card_data_exposure}</div><?php endif; ?>
</div>
<?php bank_foot();
