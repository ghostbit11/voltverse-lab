<?php
require_once __DIR__ . '/core.php';

/* Voltmart — premium, professional e-commerce UI. */
function store_css(): string { return '
:root{--ink:#0f172a;--sub:#64748b;--line:#e9edf3;--bg:#f6f7f9;--accent:#4f46e5;--accent-d:#4338ca;
--amber:#f59e0b;--green:#0a8a3f;--rose:#e11d48}
*{box-sizing:border-box}body{margin:0;font-family:"Inter","Segoe UI",Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink);-webkit-font-smoothing:antialiased}
a{color:var(--accent);text-decoration:none}img{max-width:100%}
.strip{background:#0b1220;color:#9fb0cc;font-size:.76rem;padding:.4rem 1.6rem;display:flex;gap:1rem;align-items:center}
.strip a{color:#c7d5ee}.strip .sp{margin-left:auto}
.hdr{background:#0f172a;color:#fff;display:flex;align-items:center;gap:1.6rem;padding:.85rem 1.6rem;position:sticky;top:0;z-index:30;box-shadow:0 1px 0 rgba(255,255,255,.05),0 8px 30px rgba(2,6,23,.35)}
.hdr .logo{display:flex;align-items:center;gap:.55rem;font-weight:800;font-size:1.3rem;color:#fff;letter-spacing:-.3px}
.hdr .logo .mk{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#22d3ee);display:grid;place-items:center;font-size:1.05rem;box-shadow:0 6px 16px rgba(99,102,241,.5)}
.hdr .logo small{display:block;font-size:.58rem;color:#94a3b8;font-weight:500;letter-spacing:2px;text-transform:uppercase;margin-top:-2px}
.srch{flex:1;max-width:640px;display:flex;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(2,6,23,.25)}
.srch input{flex:1;border:0;padding:.72rem 1rem;font-size:.95rem;outline:none;color:var(--ink)}
.srch button{border:0;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;padding:0 1.4rem;font-weight:700;cursor:pointer}
.hdr .acct{display:flex;gap:1.6rem;align-items:center;font-size:.85rem}
.hdr .acct a{color:#e2e8f0;display:flex;flex-direction:column;line-height:1.15}
.hdr .acct .lab{font-size:.68rem;color:#94a3b8}.hdr .acct b{font-weight:600}
.hdr .cart{flex-direction:row!important;align-items:center;gap:.45rem;font-weight:700}
.hdr .cart .n{background:linear-gradient(135deg,#f59e0b,#f97316);color:#0b1220;border-radius:999px;padding:1px 8px;font-size:.72rem;font-weight:800}
.catbar{background:#fff;border-bottom:1px solid var(--line);display:flex;gap:.2rem;padding:.55rem 1.6rem;font-size:.88rem;overflow:auto}
.catbar a{color:#334155;padding:.35rem .85rem;border-radius:8px;white-space:nowrap;font-weight:500;transition:.15s}
.catbar a:hover{background:#f1f3f8;color:var(--accent)}
main{max-width:1320px;margin:1.4rem auto;padding:0 1.6rem}
.crumb{font-size:.82rem;color:var(--sub);margin:.3rem 0 1.1rem}.crumb a{color:var(--accent)}
.promo{background:linear-gradient(120deg,#111827,#312e81);color:#fff;border-radius:18px;padding:1.6rem 2rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1.4rem;box-shadow:0 18px 44px rgba(49,46,129,.28)}
.promo h3{margin:0;font-size:1.5rem;font-weight:800}.promo p{margin:.25rem 0 0;color:#c7cbe8}
.pgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(224px,1fr));gap:1.1rem}
.pcard{background:#fff;border:1px solid var(--line);border-radius:16px;padding:1rem;display:flex;flex-direction:column;transition:.2s cubic-bezier(.2,.7,.3,1);position:relative}
.pcard:hover{box-shadow:0 18px 40px rgba(2,6,23,.12);transform:translateY(-4px);border-color:#dfe4ee}
.pimg{height:180px;display:grid;place-items:center;font-size:5rem;background:#f7f8fb;border-radius:12px;margin-bottom:.8rem}
.pcard .ttl{font-size:.94rem;color:var(--ink);font-weight:600;min-height:2.6em;line-height:1.35}
.pcard .ttl a{color:var(--ink)}.pcard .ttl a:hover{color:var(--accent)}
.rate{display:inline-flex;align-items:center;gap:6px;margin:.5rem 0}
.rbadge{background:var(--green);color:#fff;font-size:.72rem;font-weight:700;border-radius:6px;padding:2px 7px;display:inline-flex;gap:3px;align-items:center}
.rcount{color:var(--sub);font-size:.78rem}
.price{font-size:1.3rem;font-weight:800;color:var(--ink)}
.mrp{color:#94a3b8;text-decoration:line-through;font-size:.85rem;margin:0 .4rem}
.off{color:var(--green);font-size:.85rem;font-weight:700}
.badge-deal{position:relative}.badge-deal::before{content:"DEAL";position:absolute;top:10px;left:10px;background:#fff;color:var(--rose);font-size:.6rem;font-weight:800;letter-spacing:.5px;padding:3px 8px;border-radius:6px;box-shadow:0 2px 8px rgba(2,6,23,.12)}
.deliver{color:var(--green);font-size:.82rem;margin:.5rem 0;font-weight:600}
.btn{display:inline-block;border:0;border-radius:10px;padding:.72rem 1.2rem;font-weight:700;cursor:pointer;text-align:center;font-size:.92rem;transition:.15s}
.btn-cart{background:var(--accent);color:#fff}.btn-cart:hover{background:var(--accent-d)}
.btn-buy{background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff}.btn-buy:hover{filter:brightness(1.05)}
.btn-blue{background:var(--ink);color:#fff}.btn-ghost{background:#fff;color:var(--accent);border:1px solid #dbe1ee}
.btn.full{display:block;width:100%}
.pdp{display:grid;grid-template-columns:420px 1fr;gap:1.6rem;background:#fff;border-radius:18px;padding:1.6rem;border:1px solid var(--line)}
.pdp .gallery{position:sticky;top:90px;align-self:start}
.pdp .mainimg{height:380px;display:grid;place-items:center;font-size:11rem;background:#f7f8fb;border-radius:16px}
.pdp .buys{display:flex;gap:.8rem;margin-top:1rem}
.pdp h1{font-size:1.5rem;font-weight:700;margin:.2rem 0 .5rem;letter-spacing:-.3px}
.pblock{margin:.6rem 0}.pblock .now{font-size:2rem;font-weight:800}
.section{background:#fff;border:1px solid var(--line);border-radius:16px;padding:1.4rem;margin-top:1rem}
.specs td{padding:.5rem .6rem;border-bottom:1px solid #f2f4f8;font-size:.9rem}.specs td:first-child{color:var(--sub);width:180px}
.offers li{margin:.4rem 0;color:#334155}
.cartwrap{display:grid;grid-template-columns:1fr 360px;gap:1.2rem;align-items:start}
.cartitem{display:flex;gap:1.2rem;background:#fff;border:1px solid var(--line);border-radius:16px;padding:1.2rem;margin-bottom:.9rem}
.cartitem .ci{font-size:3.6rem;width:120px;display:grid;place-items:center;background:#f7f8fb;border-radius:12px}
.qty{display:inline-flex;align-items:center;border:1px solid #d7deea;border-radius:10px;overflow:hidden}
.qty a{padding:.25rem .8rem;color:var(--accent);font-weight:800}.qty span{padding:.25rem .9rem;border-left:1px solid #d7deea;border-right:1px solid #d7deea}
.summary{background:#fff;border:1px solid var(--line);border-radius:16px;padding:1.4rem;position:sticky;top:90px}
.summary h3{color:var(--sub);font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--line);padding-bottom:.7rem}
.srow{display:flex;justify-content:space-between;margin:.65rem 0;font-size:.92rem}
.srow.total{font-weight:800;font-size:1.1rem;border-top:1px dashed #dbe1ee;padding-top:.8rem;margin-top:.8rem}
.field{margin-bottom:.9rem}.field label{display:block;font-size:.76rem;color:var(--sub);margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.3px;font-weight:600}
.field input,.field textarea,.field select{width:100%;padding:.72rem .9rem;border:1px solid #d7deea;border-radius:10px;font-family:inherit;font-size:.95rem;background:#fff}
.field input:focus,.field textarea:focus{border-color:var(--accent);outline:none;box-shadow:0 0 0 3px rgba(79,70,229,.12)}
.two{display:grid;grid-template-columns:1fr 1fr;gap:.9rem}
.paycard{background:linear-gradient(135deg,#1e1b4b,#4f46e5);color:#fff;border-radius:16px;padding:1.4rem;max-width:380px;box-shadow:0 18px 40px rgba(79,70,229,.32)}
.paycard .num{font-family:ui-monospace,monospace;font-size:1.35rem;letter-spacing:2px;margin:1.3rem 0}
.flag{background:#ecfdf5;border:1px dashed #10b981;color:#065f46;border-radius:10px;padding:.75rem 1rem;margin:.6rem 0;font-family:ui-monospace,monospace}
.notice{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:.75rem 1rem;margin:.6rem 0;font-size:.9rem}
footer{background:#0b1220;color:#8fa1bd;margin-top:2.5rem;padding:2rem 1.6rem;text-align:center;font-size:.82rem}
footer a{color:#c7d5ee}
@media(max-width:860px){.pdp,.cartwrap{grid-template-columns:1fr}.pdp .mainimg{font-size:7rem;height:240px}}
'; }

function store_head(string $title): void {
    $lv = level();
    $cart = json_decode($_COOKIE['st_cart'] ?? '{}', true) ?: [];
    $n = array_sum(array_map('intval', $cart));
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . ' · Voltmart</title>
<style>' . store_css() . '</style></head><body>
<div class="strip">🚚 Free delivery over $50 · Deliver to India
  <span class="sp"><a href="/dashboard.php">← All apps</a> · Difficulty: <a href="/level.php">' . strtoupper($lv) . '</a></span></div>
<div class="hdr">
  <a class="logo" href="/store/"><span class="mk"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="#fff" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14l-1.2 12.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8L5 7z"/><path d="M8.5 7V5.5a3.5 3.5 0 0 1 7 0V7"/></svg></span><span>Voltmart<small>online shopping</small></span></a>
  <form class="srch" action="/store/search.php" method="get"><input type="search" name="q" placeholder="Search for products, brands and more"><button type="submit">🔍 Search</button></form>
  <div class="acct">
    <a href="/store/account.php"><span class="lab">Hello, sign in</span><b>Account ▾</b></a>
    <a class="cart" href="/store/cart.php">🛒 Cart' . ($n ? ' <span class="n">' . $n . '</span>' : '') . '</a>
  </div>
</div>
<div class="catbar">
  <a href="/store/">Home</a><a href="/store/search.php?q=Laptops">Laptops</a><a href="/store/search.php?q=Phones">Phones</a>
  <a href="/store/search.php?q=Audio">Audio</a><a href="/store/search.php?q=Wearables">Wearables</a>
  <a href="/store/search.php?q=Tablets">Tablets</a><a href="/store/search.php?q=Accessories">Accessories</a>
  <a href="/store/search.php?q=Displays">Displays</a>
</div>
<main>';
}

function store_foot(): void {
    echo '</main><footer>© Voltmart — a Voltmart Retail company · Powered by the BREACHR range ·
      <a href="/dashboard.php">Apps</a> · <a href="/level.php">Difficulty</a></footer></body></html>';
}

function mrp($price){ return round($price * 1.35); }
function off($price){ return (int)round((mrp($price)-$price)/mrp($price)*100); }
function rcount($id){ return (($id*7919) % 4800) + 120; }
