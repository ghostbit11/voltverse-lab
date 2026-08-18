<?php
require_once __DIR__ . '/../inc/store_layout.php';
require_login();
$secure = lvl_secure();
$msg = null; $flag = false; $url = null;
$allowed = ['jpg','jpeg','png','gif','webp'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['avatar']['name'])) {
    $name = preg_replace('/[^a-zA-Z0-9._\-]/','_', basename($_FILES['avatar']['name']));
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if ($secure && !in_array($ext, $allowed)) {
        $msg = "Rejected: only image files (".implode(', ',$allowed).") are allowed.";
    } else {
        $dest = __DIR__ . "/uploads/$name";
        if (@move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
            $url = "/store/uploads/$name"; $msg = "Uploaded to $url";
            if (!$secure && in_array($ext, ['php','phtml','phar','php5','sh','cgi'])) {
                $flag = true;   // a server-executable file was accepted → webshell / RCE
            }
        } else $msg = "Upload failed.";
    }
}
store_head('Upload avatar');
?>
<div class="crumb"><a href="/store/account.php">Account</a> › Avatar</div>
<div class="section" style="max-width:560px">
  <h2 style="margin-top:0">Profile picture</h2>
  <p style="color:#64748b">Upload a new avatar (image).</p>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="avatar" style="margin:.6rem 0">
    <div><button class="btn btn-cart" type="submit">Upload</button></div>
  </form>
  <?php if ($msg): ?><div class="notice" style="margin-top:1rem"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($url): ?><p><a href="<?= e($url) ?>" target="_blank">Open uploaded file ↗</a></p><?php endif; ?>
  <?php if ($flag): ?><div class="flag">🚩 A server-executable file was accepted — unrestricted file upload → webshell / RCE! Flag: VOLT{store_unrestricted_upload}</div><?php endif; ?>
  <?php if (!$secure): ?><div class="notice">💡 There's no real file-type check here. Try uploading <code>shell.php</code>.</div><?php endif; ?>
</div>
<?php store_foot();
