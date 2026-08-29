<?php
require_once __DIR__ . '/core.php';

/* Generic clean marketing/corporate website layout (used by microsite + corp site). */
function site_head(string $title, array $cfg): void {
    require_login();
    if (!empty($cfg['lab'])) lab_guard($cfg['lab']);
    $accent = '#4f46e5';   // unified VoltVerse brand accent across all apps
    $brand  = $cfg['brand'] ?? 'Website';
    $ico    = $cfg['ico'] ?? '🌐';
    $home   = $cfg['home'] ?? '/';
    $nav    = $cfg['nav'] ?? [];
    $lv = level();
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . ' · ' . e($brand) . '</title>
<style>
:root{--acc:' . $accent . '}
*{box-sizing:border-box}body{margin:0;font-family:Inter,"Segoe UI",system-ui,sans-serif;background:#fff;color:#0f172a;-webkit-font-smoothing:antialiased}
a{color:var(--acc);text-decoration:none}
.strip{background:#0b1220;color:#93a4c6;font-size:.75rem;padding:.35rem 1.6rem;display:flex}.strip .sp{margin-left:auto}.strip a{color:#c7d5ee}
.nav{position:sticky;top:0;z-index:20;display:flex;align-items:center;gap:2rem;padding:1rem 2rem;background:rgba(255,255,255,.9);backdrop-filter:blur(10px);border-bottom:1px solid #eef1f6}
.nav .brand{font-weight:800;font-size:1.25rem;display:flex;align-items:center;gap:.5rem}
.nav .brand .mk{width:32px;height:32px;border-radius:9px;background:var(--acc);display:grid;place-items:center;color:#fff;font-size:1rem}
.nav .links{display:flex;gap:1.6rem;font-weight:500;color:#334155;font-size:.92rem}.nav .links a{color:#334155}.nav .links a:hover{color:var(--acc)}
.nav .cta{margin-left:auto;background:var(--acc);color:#fff;padding:.55rem 1.1rem;border-radius:10px;font-weight:700}
main{max-width:1120px;margin:0 auto;padding:0 2rem}
.hero{text-align:center;padding:4rem 1rem 3rem}
.hero .eyebrow{color:var(--acc);font-weight:700;letter-spacing:2px;font-size:.72rem;text-transform:uppercase}
.hero h1{font-size:3rem;font-weight:900;letter-spacing:-1.5px;margin:.8rem 0;line-height:1.05}
.hero p{color:#64748b;font-size:1.15rem;max-width:620px;margin:0 auto}
.btn{display:inline-block;background:var(--acc);color:#fff;border:0;border-radius:10px;padding:.72rem 1.35rem;font-weight:700;cursor:pointer;font-size:.95rem}
.btn.ghost{background:#fff;color:var(--acc);border:1px solid #dbe1ee}
.feats{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.2rem;margin:2rem 0}
.feat{border:1px solid #eef1f6;border-radius:16px;padding:1.6rem;background:#fbfcfe}
.feat .ic{font-size:2rem}.feat h3{margin:.6rem 0 .3rem}.feat p{color:#64748b;font-size:.9rem;margin:0}
.card{border:1px solid #eef1f6;border-radius:16px;padding:1.6rem;margin:1.2rem 0;background:#fff;box-shadow:0 1px 3px rgba(2,6,23,.04)}
.field{margin-bottom:.9rem}.field label{display:block;font-size:.78rem;color:#64748b;margin-bottom:.3rem;font-weight:600}
.field input,.field textarea{width:100%;padding:.7rem .9rem;border:1px solid #d7deea;border-radius:10px;font-family:inherit;font-size:.95rem}
.field input:focus{border-color:var(--acc);outline:none}
.flag{background:#ecfdf5;border:1px dashed #10b981;color:#065f46;border-radius:10px;padding:.75rem 1rem;margin:.7rem 0;font-family:ui-monospace,monospace}
.notice{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:.75rem 1rem;margin:.7rem 0;font-size:.9rem}
pre{white-space:pre-wrap;background:#0b1020;color:#93c5fd;padding:1rem;border-radius:10px;overflow:auto;font-size:.82rem}
footer{background:#0b1220;color:#8fa1bd;margin-top:3rem;padding:2rem;text-align:center;font-size:.82rem}footer a{color:#c7d5ee}
</style></head><body>
<div class="strip">🔒 ' . e($brand) . ' — part of the VoltVerse group <span class="sp"><a href="/dashboard.php">← All apps</a> · Difficulty: <a href="/level.php">' . strtoupper($lv) . '</a></span></div>
<div class="nav"><a class="brand" href="' . e($home) . '"><span class="mk">' . $ico . '</span>' . e($brand) . '</a>
<div class="links">';
    foreach ($nav as [$lbl,$href]) echo '<a href="' . e($href) . '">' . e($lbl) . '</a>';
    echo '</div><a class="cta" href="' . e($cfg['cta_href'] ?? $home) . '">' . e($cfg['cta'] ?? 'Get started') . '</a></div><main>';
}
function site_foot(string $brand): void {
    echo '</main><footer>© ' . e($brand) . ' · part of the VoltVerse group · <a href="/dashboard.php">← All apps</a></footer></body></html>';
}
