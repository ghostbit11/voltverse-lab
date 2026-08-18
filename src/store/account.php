<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$stu = $_COOKIE['st_user'] ?? 'customer@volt.local';
// A01/A07 · CSRF: state change with NO anti-CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    db()->prepare("UPDATE users SET name=? WHERE email=?")->execute([$_POST['name'], $stu]);
    header('Location: /store/account.php'); exit;
}
$st = db()->prepare("SELECT * FROM users WHERE email=?"); $st->execute([$stu]);
$cust = $st->fetch(PDO::FETCH_ASSOC) ?: ['email'=>$stu,'name'=>$stu,'is_admin'=>0];
$secure = lvl_secure();
$dispName = $secure ? e($cust['name']) : $cust['name'];   // A03 · stored XSS: name rendered raw at Low
$xss = (!$secure && preg_match('/<script|onerror=/i',(string)$cust['name']));
store_head('My account');
$os = db()->prepare("SELECT * FROM orders WHERE user_email=? ORDER BY id DESC"); $os->execute([$stu]);
$orders = $os->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="crumb"><a href="/store/">Home</a> › My account</div>
<div class="section">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
    <div><div style="font-size:1.2rem;font-weight:600">Hello, <?= $dispName ?: e($cust['email']) ?></div>
      <div style="color:#878787"><?= e($cust['email']) ?></div></div>
    <div><a class="btn btn-ghost" href="/store/admin.php">Admin</a>
      <a class="btn btn-ghost" href="/store/login.php">Switch account</a>
      <a class="btn btn-ghost" href="/store/login.php?as=customer">Reset demo</a></div>
  </div>
  <?php if (!empty($cust['is_admin'])): ?>
    <div class="flag" style="margin-top:1rem">🔐 Signed in as ADMIN — auth bypass successful. Flag: VOLT{store_sqli_auth_bypass}</div>
  <?php endif; ?>
  <?php if ($xss): ?><div class="flag" style="margin-top:1rem">🚩 Stored XSS in your profile name executed. Flag: VOLT{store_profile_xss}</div><?php endif; ?>
  <form method="post" class="two" style="margin-top:1rem;align-items:end">
    <div class="field"><label>Display name (no CSRF token — try changing it from another site)</label><input name="name" value="<?= e($cust['name']) ?>"></div>
    <div><button class="btn btn-blue">Save profile</button></div>
  </form>
</div>
<div class="section">
  <h2 style="margin-top:0">Account tools</h2>
  <div style="display:flex;gap:.7rem;flex-wrap:wrap">
    <a class="btn btn-ghost" href="/store/upload.php">🖼 Upload avatar</a>
    <a class="btn btn-ghost" href="/store/card.php">🎁 Gift card</a>
    <a class="btn btn-ghost" href="/store/import.php">📥 Import wishlist</a>
    <a class="btn btn-ghost" href="/store/admin.php">🛠 Admin</a>
  </div>
</div>
<div class="section">
  <h2 style="margin-top:0">My orders</h2>
  <table style="width:100%"><tr style="text-align:left;color:#878787;font-size:.8rem"><th style="padding:.5rem">Order</th><th>Status</th><th>Total</th></tr>
  <?php foreach ($orders as $o): ?>
    <tr style="border-top:1px solid #f0f0f0"><td style="padding:.6rem .5rem"><a href="/store/order.php?id=<?= (int)$o['id'] ?>">#<?= (int)$o['id'] ?></a></td>
      <td><?= e($o['status']) ?></td><td>$<?= number_format($o['total'],2) ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$orders): ?><tr><td colspan="3" style="padding:.6rem .5rem;color:#878787">No orders yet.</td></tr><?php endif; ?>
  </table>
</div>
<?php store_foot();
