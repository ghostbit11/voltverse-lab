<?php
require_once __DIR__ . '/../inc/core.php';
require_once __DIR__ . '/../inc/bank_layout.php';
$secure = lvl_secure();
$me = 'customer@volt.local';

// AJAX claim (used by the "parallel" race button) — handled before any HTML
if (($_GET['ajax'] ?? '') === '1' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    header('Content-Type: application/json');
    $claims = (int)($_SESSION['voucher_claims'] ?? 0);
    // SECURE: voucher can be claimed only once (atomic check). LOW: no lock → double-spend / race.
    if ($secure && $claims >= 1) { echo json_encode(['ok'=>false,'msg'=>'already claimed','claims'=>$claims]); exit; }
    $claims++; $_SESSION['voucher_claims'] = $claims;
    db()->prepare("UPDATE accounts SET balance=balance+1000 WHERE owner=?")->execute([$me]);
    $flag = (!$secure && $claims >= 2) ? 'VOLT{bank_race_condition}' : null;
    echo json_encode(['ok'=>true,'claims'=>$claims,'flag'=>$flag]); exit;
}

bank_head('Rewards', 'dashboard');
$acc = db()->query("SELECT * FROM accounts WHERE owner='" . $me . "'")->fetch(PDO::FETCH_ASSOC);
?>
<div style="max-width:640px">
  <div class="card balcard"><div class="lab">Current balance</div><div class="amt">$<?= number_format($acc['balance'],2) ?></div></div>
  <div class="card" style="margin-top:1rem">
    <h2 style="margin-top:0">🎁 Welcome voucher — $1,000</h2>
    <p style="color:#5a6b82">A one-time $1,000 bonus. Claim it to your account.</p>
    <button class="btn btn-p" onclick="claim(1)">Claim voucher</button>
    <button class="btn btn-g" onclick="claim(5)">⚡ Claim ×5 (parallel)</button>
    <div id="out" style="margin-top:1rem"></div>
  </div>
  <div style="color:#8496ad;font-size:.82rem;margin-top:.4rem">This welcome voucher can be redeemed once per account. Credits appear instantly.</div>
</div>
<script>
async function claim(n){var out=document.getElementById('out');out.innerHTML='claiming…';
  var ps=[];for(var i=0;i<n;i++)ps.push(fetch('/bank/redeem.php?ajax=1',{method:'POST'}).then(r=>r.json()));
  var res=await Promise.all(ps);var last=res[res.length-1];var flag=res.find(r=>r.flag);
  out.innerHTML='<div style="color:#137333;font-weight:700">Claimed '+res.filter(r=>r.ok).length+'× · voucher claim count: '+last.claims+'</div>'+
    (flag?'<div style="background:#e6f4ea;border:1px dashed #137333;color:#0d652d;border-radius:8px;padding:.7rem;margin-top:.5rem;font-family:monospace">🚩 '+flag.flag+'</div>':'');
  setTimeout(()=>location.reload(),1400);
}
</script>
<?php bank_foot();
