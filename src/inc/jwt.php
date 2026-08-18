<?php
// Minimal JWT helpers for the VoltID lab (intentionally weak).
function b64u(string $d): string { return rtrim(strtr(base64_encode($d), '+/', '-_'), '='); }
function b64u_dec(string $d): string { return base64_decode(strtr($d, '-_', '+/')); }

const JWT_WEAK_SECRET = 'secret';   // guessable HMAC key (the whole point)

function jwt_make(array $payload, string $alg = 'HS256', string $secret = JWT_WEAK_SECRET): string {
    $h = b64u(json_encode(['alg'=>$alg,'typ'=>'JWT']));
    $p = b64u(json_encode($payload));
    $sig = ($alg === 'none') ? '' : b64u(hash_hmac('sha256', "$h.$p", $secret, true));
    return "$h.$p.$sig";
}
function jwt_parse(string $tok): array {
    $parts = array_pad(explode('.', $tok), 3, '');
    return [json_decode(b64u_dec($parts[0]), true) ?: [], json_decode(b64u_dec($parts[1]), true) ?: [], $parts[2], $parts[0], $parts[1]];
}
// Returns the verified payload, or null. $secure toggles the two flaws off.
function jwt_verify(string $tok, bool $secure): ?array {
    [$hd, $pl, $sig, $h, $p] = jwt_parse($tok);
    $alg = strtolower($hd['alg'] ?? '');
    if ($alg === 'none') return $secure ? null : $pl;                 // VULN: 'none' accepted at Low
    $secret = $secure ? 'S6!x9_strong_random_key_change_me' : JWT_WEAK_SECRET; // VULN: weak secret at Low
    $expected = b64u(hash_hmac('sha256', "$h.$p", $secret, true));
    return hash_equals($expected, (string)$sig) ? $pl : null;
}
