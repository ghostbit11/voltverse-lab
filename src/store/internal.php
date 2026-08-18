<?php
// SSRF target: an "internal metadata" endpoint only meant to be reachable from the server itself.
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
header('Content-Type: text/plain');
if (in_array($ip, ['127.0.0.1','::1','localhost']) || strpos($ip,'172.') === 0) {
    echo "VoltVerse internal metadata service\n";
    echo "db_password: voltverse123\n";
    echo "internal_api_key: VOLT{store_ssrf_internal_metadata}\n";
} else {
    http_response_code(403);
    echo "403 Forbidden — internal service (only reachable from the server).";
}
