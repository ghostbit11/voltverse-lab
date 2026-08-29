<?php
// Renamed to /admin.php — keep this path working for old links/bookmarks.
require_once __DIR__ . '/inc/core.php';
$qs = $_SERVER['QUERY_STRING'] ?? '';
header('Location: /admin.php' . ($qs ? '?' . $qs : ''));
exit;
