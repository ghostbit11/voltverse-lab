<?php
require_once __DIR__ . '/core.php';

/* Aurora Bank — realistic net-banking layout (professional, not a "lab"). */
function bank_css(): string { return '
*{box-sizing:border-box}body{margin:0;font-family:Inter,"Segoe UI",Roboto,sans-serif;background:#eef2f7;color:#0f2033}
a{color:#4f46e5;text-decoration:none}
.bwrap{display:grid;grid-template-columns:250px 1fr;min-height:100vh}
.side{background:#0f172a;color:#c7d5e8;padding:0}
.brand{display:flex;align-items:center;gap:.6rem;padding:1.2rem 1.3rem;font-weight:800;font-size:1.25rem;color:#fff;border-bottom:1px solid rgba(255,255,255,.08)}
.brand .dot{width:30px;height:30px;border-radius:9px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:grid;place-items:center;font-size:1rem}
.snav{padding:1rem .7rem;display:flex;flex-direction:column;gap:.2rem}
.snav a{display:flex;align-items:center;gap:.8rem;padding:.7rem .9rem;border-radius:10px;color:#c7d5e8;font-weight:500}
.snav a:hover{background:rgba(255,255,255,.06);color:#fff}
.snav a.on{background:linear-gradient(90deg,rgba(99,102,241,.22),transparent);color:#fff;box-shadow:inset 3px 0 0 #6366f1}
.snav .ic{width:20px;text-align:center}
.side .foot{margin-top:auto;padding:1rem 1.3rem;font-size:.72rem;color:#6b83a3}
.main{display:flex;flex-direction:column}
.thead{background:#fff;border-bottom:1px solid #e3e9f0;padding:.9rem 1.6rem;display:flex;align-items:center;gap:1rem}
.thead h1{font-size:1.15rem;margin:0}
.thead .sp{margin-left:auto;display:flex;align-items:center;gap:1rem;font-size:.85rem;color:#5a6b82}
.secure-badge{color:#137333;background:#e6f4ea;border-radius:999px;padding:3px 11px;font-weight:700;font-size:.72rem}
.content{padding:1.6rem;max-width:1200px}
.cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem}
.card{background:#fff;border:1px solid #e3e9f0;border-radius:14px;padding:1.3rem;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.balcard{background:linear-gradient(135deg,#1e1b4b,#4f46e5);color:#fff;border:0}
.balcard .lab{opacity:.8;font-size:.8rem}.balcard .amt{font-size:2.2rem;font-weight:800;margin:.3rem 0}
.balcard .num{font-family:ui-monospace,monospace;letter-spacing:2px;opacity:.9}
.qa{display:flex;gap:.8rem;flex-wrap:wrap;margin:1.2rem 0}
.qa a{flex:1;min-width:120px;background:#fff;border:1px solid #e3e9f0;border-radius:12px;padding:1rem;text-align:center;color:#0f2033;font-weight:600}
.qa a:hover{border-color:#4f46e5;box-shadow:0 4px 14px rgba(26,86,219,.12)}
.qa .ic{font-size:1.5rem;display:block;margin-bottom:.3rem}
h2{font-size:1.05rem}
table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:.65rem .5rem;border-bottom:1px solid #eef2f7;font-size:.9rem}
th{color:#8496ad;font-size:.72rem;text-transform:uppercase;letter-spacing:.5px}
.credit{color:#137333;font-weight:700}.debit{color:#c5221f;font-weight:700}
.field{margin-bottom:.9rem}.field label{display:block;font-size:.78rem;color:#5a6b82;margin-bottom:.3rem;font-weight:600}
.field input,.field select{width:100%;padding:.7rem .8rem;border:1px solid #d4dce6;border-radius:8px;font-family:inherit;font-size:.95rem}
.field input:focus{border-color:#4f46e5;outline:none}
.btn{display:inline-block;border:0;border-radius:10px;padding:.72rem 1.3rem;font-weight:700;cursor:pointer;font-size:.93rem}
.btn-p{background:#4f46e5;color:#fff}.btn-g{background:#fff;color:#4f46e5;border:1px solid #cdd8e6}.btn.full{display:block;width:100%}
.flag{background:#e6f4ea;border:1px dashed #137333;color:#0d652d;border-radius:8px;padding:.7rem 1rem;margin:.7rem 0;font-family:ui-monospace,monospace}
.warn{background:#fef7e0;border:1px solid #fddf8f;border-radius:8px;padding:.7rem 1rem;margin:.7rem 0;font-size:.9rem}
.chip{background:linear-gradient(135deg,#111,#333);color:#fff;border-radius:14px;padding:1.4rem;max-width:340px}
.chip .no{font-family:ui-monospace,monospace;font-size:1.25rem;letter-spacing:2px;margin:1.4rem 0 .8rem}
@media(max-width:820px){.bwrap{grid-template-columns:1fr}.side{display:none}}
'; }

function bank_head(string $title, string $active='dashboard'): void {
    require_login();
    $u = pf_user();
    $nav = ['dashboard'=>['🏠','Dashboard','/bank/'],'transfer'=>['💸','Transfer','/bank/transfer.php'],
            'statements'=>['📄','Statements','/bank/statements.php'],'cards'=>['💳','Cards','/bank/cards.php'],
            'beneficiaries'=>['👥','Beneficiaries','/bank/index.php']];
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . ' · Aurora Bank</title>
<style>' . bank_css() . '</style></head><body><div class="bwrap">
<aside class="side"><div class="brand"><span class="dot">◆</span> Aurora<span style="color:#818cf8">Bank</span></div>
<nav class="snav">';
    foreach ($nav as $k=>[$ic,$lbl,$href]) echo '<a class="'.($k===$active?'on':'').'" href="'.$href.'"><span class="ic">'.$ic.'</span>'.$lbl.'</a>';
    echo '<a href="/dashboard.php"><span class="ic">↩</span>Back to range</a></nav>
<div class="foot">Aurora Bank · IFSC AURB0001 · FDIC (demo)</div></aside>
<div class="main"><div class="thead"><h1>' . e($title) . '</h1>
  <div class="sp"><span class="secure-badge">🔒 Secure session</span>Hello, ' . e($u['name']) . ' · <a href="/logout.php">Sign out</a></div></div>
<div class="content">';
}
function bank_foot(): void { echo '</div></div></div></body></html>'; }
