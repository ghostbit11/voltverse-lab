<?php
require_once __DIR__ . '/inc/core.php';
require_once __DIR__ . '/inc/catalog.php';
require_login();
$me = pf_user();
if (!is_admin_user()) { header('Location: /dashboard.php'); exit; }
$iAmSuper = is_superadmin();
$total = count(CATALOG());
ensure_solves();

$rows = db()->query("SELECT email,name,role FROM platform_users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$out = [];
foreach ($rows as $r) {
    if ($r['role'] === 'superadmin') continue;
    if (!$iAmSuper && org_owner($r['email']) !== $me['email']) continue;   // admin → own team only
    $solved = solved_ids($r['email']);
    // most recent solve time
    $ls = db()->prepare("SELECT MAX(ts) FROM solves WHERE player=?"); $ls->execute([$r['email']]);
    $out[] = [
        'name'      => $r['name'],
        'email'     => $r['email'],
        'role'      => $r['role'],
        'org'       => org_name($r['email']),
        'points'    => player_points($r['email']),
        'solved'    => count($solved),
        'total'     => $total,
        'assigned'  => count(assigned_ids($r['email'])),
        'streak'    => player_streak($r['email']),
        'last_active' => $ls->fetchColumn() ?: '',
    ];
}
usort($out, fn($a,$b)=>$b['points']<=>$a['points']);

$csv = function($v){ return '"' . str_replace('"','""',(string)$v) . '"'; };
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="voltverse-scores-' . date('Y-m-d') . '.csv"');
echo "Name,Email,Role,Organisation,Points,Solved,Total,Assigned,Day streak,Last active\n";
foreach ($out as $r) {
    echo implode(',', [
        $csv($r['name']), $csv($r['email']), $csv($r['role']), $csv($r['org']),
        $r['points'], $r['solved'], $r['total'], $r['assigned'], $r['streak'], $csv($r['last_active']),
    ]) . "\n";
}
