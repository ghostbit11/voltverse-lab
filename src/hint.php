<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/hints.php';
require_login();
header('Content-Type: application/json');
$u = pf_user();
if (!is_admin_user() && !member_hints_on($u['email'])) { echo json_encode(['ok'=>false,'error'=>'Hints are disabled by your admin.']); exit; }
$cid = trim($_POST['cid'] ?? '');
$idx = (int)($_POST['idx'] ?? -1);
$hints = hints_for($cid);
if (!$hints || $idx < 0 || $idx >= count($hints)) { echo json_encode(['ok'=>false,'error'=>'No such hint']); exit; }
$already = in_array($idx, unlocked_hints($u['email'], $cid), true);
unlock_hint($u['email'], $cid, $idx);
echo json_encode([
  'ok'=>true,
  'hint'=>$hints[$idx],
  'idx'=>$idx,
  'total'=>count($hints),
  'cost'=>$already ? 0 : HINT_COST,
  'charged'=>!$already,
  'points'=>player_points($u['email']),
]);
