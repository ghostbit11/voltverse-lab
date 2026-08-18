<?php
require_once __DIR__ . '/../inc/core.php';
require_login();
// A01 · Open Redirect
$url = $_GET['url'] ?? '/product-site/';
$external = (bool)preg_match('#^https?://#i', $url);
if (lvl_secure()) {
    // SECURE: only allow same-site relative paths
    if (!preg_match('#^/[a-z0-9/_\-\?\.=&%]*$#i', $url)) $url = '/product-site/';
    header('Location: ' . $url); exit;
}
// VULN: redirect anywhere. Show an interstitial with the flag on external redirects.
if ($external) {
    echo '<!doctype html><meta charset="utf-8"><title>Redirecting…</title>
<meta http-equiv="refresh" content="4;url=' . htmlspecialchars($url, ENT_QUOTES) . '">
<div style="font-family:Inter,system-ui;max-width:560px;margin:12vh auto;text-align:center;color:#0f172a">
  <div style="font-size:2.4rem">↗️</div><h2>Redirecting you to a partner site…</h2>
  <p style="color:#64748b">Taking you to <b>' . htmlspecialchars($url, ENT_QUOTES) . '</b></p>
  <div style="background:#ecfdf5;border:1px dashed #10b981;color:#065f46;border-radius:10px;padding:.8rem;font-family:monospace">
    🚩 Open redirect — this page will send users to ANY external URL. Flag: VOLT{microsite_open_redirect}</div>
  <p><a href="/product-site/">← Back to VoltBook Pro</a></p></div>';
    exit;
}
header('Location: ' . $url);
