<?php
require_once __DIR__ . '/inc/catalog.php';
require_login();
header('Content-Type: application/json');
$u = pf_user();
$data = json_decode(file_get_contents('php://input'), true) ?: [];
$flag = trim($data['flag'] ?? ($_POST['flag'] ?? ''));
$c = cat_by_flag($flag);
if (!$c) { echo json_encode(['correct'=>false, 'msg'=>'Incorrect flag — keep hunting.']); exit; }
$fresh = record_solve($u['email'], $c[0]);
$first = $fresh && is_first_blood($c[0]);
echo json_encode(['correct'=>true, 'id'=>$c[0], 'title'=>$c[1], 'app'=>$c[2],
    'points'=>$c[7], 'already'=>!$fresh, 'first_blood'=>$first,
    'total_points'=>player_points($u['email']), 'solved'=>count(solved_ids($u['email'])), 'total'=>count(CATALOG())]);
