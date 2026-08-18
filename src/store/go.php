<?php
require_once __DIR__ . '/../inc/core.php';
require_login();
// A01 · Open Redirect: "continue to partner" link with an unvalidated url
$url = $_GET['url'] ?? '/store/';
if (lvl_secure()) {
    // SECURE: only allow same-site relative paths
    if (!preg_match('#^/[a-z0-9/_\-\?\.=&]*$#i', $url)) $url = '/store/';
}
// VULN: redirect anywhere (e.g. ?url=https://evil.example)
header('Location: ' . $url);
