<?php
require_once __DIR__ . '/core.php';

function css(): string { return '
:root{
  --bg:#0a0d13;--chrome:#0d1119;--surface:#111725;--surface2:#141b2b;--raise:#18202f;
  --line:#1e2637;--line2:#283345;
  --fg:#e8ecf4;--muted:#9aa7bd;--dim:#616e84;
  --accent:#4f8cff;--accent2:#4f8cff;--cyan:#4f8cff;--blue:#4f8cff;--purple:#7c5cff;
  --green:#43c06a;--amber:#e0a52e;--red:#f2564b;--hi:#ef6a45;
  --accent-soft:rgba(79,140,255,.14);--accent-line:rgba(79,140,255,.35);
  --glass:#111725;--grad:linear-gradient(135deg,#4f8cff,#7c5cff);
  --sans:system-ui,"Segoe UI",-apple-system,Roboto,Helvetica,Arial,sans-serif;
  --mono:ui-monospace,"SF Mono",Consolas,"Roboto Mono",monospace;
}
*{box-sizing:border-box}
html,body{margin:0;min-height:100%}
body{font-family:var(--sans);background:
  radial-gradient(760px 300px at 100% -80px,rgba(79,140,255,.05),transparent 70%),var(--bg);
  color:var(--fg);-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.55}
a{color:var(--accent);text-decoration:none}a:hover{color:#8bb2ff}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.fadeup{animation:fadeUp .5s cubic-bezier(.2,.7,.3,1) both}
.d1{animation-delay:.05s}.d2{animation-delay:.12s}.d3{animation-delay:.2s}.d4{animation-delay:.28s}
.gradtext{background:linear-gradient(120deg,#8bb2ff,#b39dff);-webkit-background-clip:text;background-clip:text;color:transparent}
/* app shell */
.shell{display:grid;grid-template-columns:230px 1fr;min-height:100vh}
.rail{position:sticky;top:0;height:100vh;background:var(--chrome);border-right:1px solid var(--line);
  padding:16px 13px;display:flex;flex-direction:column;gap:20px;overflow:auto}
.rail .brand{display:flex;align-items:center;gap:9px;padding:4px 7px 2px}
.rail .brand b{font-size:15px;font-weight:600;letter-spacing:-.01em;color:#fff}
.railcap{font:600 10px/1 var(--sans);letter-spacing:.16em;text-transform:uppercase;color:var(--dim);padding:0 9px 3px}
.railnav{display:flex;flex-direction:column;gap:2px}
.railnav a{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;color:var(--muted);font-size:13.5px}
.railnav a svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:1.8;opacity:.9}
.railnav a:hover{background:#141b29;color:var(--fg)}
.railnav a.on{background:var(--accent-soft);color:#cfe0ff;box-shadow:inset 0 0 0 1px var(--accent-line)}
.railfoot{margin-top:auto;display:flex;flex-direction:column;gap:12px}
.lvlseg{display:flex;background:#0e1420;border:1px solid var(--line);border-radius:9px;padding:3px}
.lvlseg a{flex:1;text-align:center;font-size:11px;color:var(--dim);padding:5px 0;border-radius:6px}
.lvlseg a.on{color:#fff;box-shadow:inset 0 0 0 1px var(--line2)}
.lvlseg a.on.low{background:rgba(242,86,75,.16)}.lvlseg a.on.medium{background:rgba(224,165,46,.16)}
.lvlseg a.on.high{background:rgba(239,106,69,.16)}.lvlseg a.on.secure{background:rgba(67,192,106,.16)}
.mecard{display:flex;align-items:center;gap:9px;padding:8px;border-top:1px solid var(--line)}
.ava{width:30px;height:30px;border-radius:8px;background:linear-gradient(150deg,#2a3550,#22304a);display:grid;place-items:center;font-size:12px;font-weight:700;color:#c7d6f2}
.mecard .nm{flex:1;min-width:0}.mecard .nm b{font-size:12.5px;font-weight:600;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mecard .nm small{color:var(--dim);font-size:11px}
.mecard a{color:var(--dim)}.mecard a:hover{color:var(--fg)}
/* topbar */
.workarea{min-width:0;display:flex;flex-direction:column}
.topbar{position:sticky;top:0;z-index:20;display:flex;align-items:center;gap:14px;padding:12px 26px;
  background:rgba(10,13,19,.82);backdrop-filter:blur(12px);border-bottom:1px solid var(--line)}
.topbar .pt{font-size:15px;font-weight:600;letter-spacing:-.01em}
.topbar .pt small{display:block;color:var(--dim);font-size:11.5px;font-weight:400}
.topbar .sp{margin-left:auto}
main{max-width:1180px;margin:22px auto;padding:0 26px;width:100%}
/* level pill */
.lvlpill{font-size:11px;font-weight:700;padding:4px 11px;border-radius:999px;border:1px solid;letter-spacing:.03em}
.lvl-low{color:#f39089;border-color:rgba(242,86,75,.4);background:rgba(242,86,75,.1)}
.lvl-medium{color:#ecc46b;border-color:rgba(224,165,46,.4);background:rgba(224,165,46,.1)}
.lvl-high{color:#f2a288;border-color:rgba(239,106,69,.4);background:rgba(239,106,69,.1)}
.lvl-secure{color:#7ee0a0;border-color:rgba(67,192,106,.4);background:rgba(67,192,106,.1)}
/* hero */
.hero{position:relative;overflow:hidden;border:1px solid var(--line);border-radius:14px;padding:1.8rem 1.9rem;
  background:linear-gradient(180deg,var(--surface2),var(--surface))}
.hero .eyebrow{display:inline-flex;align-items:center;gap:.5rem;color:#9db8ff;font-weight:600;letter-spacing:.12em;
  font-size:11px;text-transform:uppercase;border:1px solid var(--accent-line);border-radius:999px;padding:4px 11px;background:var(--accent-soft)}
.hero h1{margin:.7rem 0 .4rem;font-size:1.9rem;font-weight:700;letter-spacing:-.02em;line-height:1.1;max-width:720px}
.hero p{color:var(--muted);max-width:600px;font-size:.95rem}
.cta{display:inline-flex;align-items:center;gap:.45rem;margin-top:1.1rem;background:var(--accent);color:#fff;font-weight:600;
  padding:.62rem 1.15rem;border-radius:9px;box-shadow:0 6px 18px -8px rgba(79,140,255,.7);transition:.16s}
.cta:hover{transform:translateY(-1px);background:#5f98ff;color:#fff}
.shield{position:absolute;right:2rem;top:50%;transform:translateY(-50%);font-size:5rem;opacity:.16}
/* app cards */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
.appcard{display:block;position:relative;border:1px solid var(--line);border-radius:12px;padding:1.2rem;overflow:hidden;
  background:var(--surface);color:var(--fg);transition:.18s}
.appcard:hover{transform:translateY(-3px);border-color:var(--line2);box-shadow:0 16px 40px -20px rgba(0,0,0,.8)}
.appcard .ico{font-size:1.7rem;width:42px;height:42px;border-radius:10px;background:var(--raise);display:grid;place-items:center;border:1px solid var(--line)}
.appcard h3{margin:.7rem 0 .25rem;font-size:1rem;font-weight:600;color:#fff}.appcard p{color:var(--muted);font-size:.84rem;min-height:auto}
.tags{display:flex;gap:.35rem;flex-wrap:wrap;margin-top:.6rem}
.tag{font-size:10px;font-weight:600;color:#9db8ff;background:var(--accent-soft);border:1px solid var(--accent-line);border-radius:6px;padding:2px 8px}
.soon{opacity:.5}.soon .tag{color:var(--dim);background:rgba(255,255,255,.03);border-color:var(--line)}
.live{position:absolute;top:1.1rem;right:1.1rem;font-size:10px;font-weight:700;color:var(--green);display:flex;align-items:center;gap:5px;letter-spacing:.04em}
.live::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--green);animation:pulse 2s infinite}
/* panels / auth */
.panel{border:1px solid var(--line);border-radius:12px;padding:1.4rem;margin-bottom:1rem;background:var(--surface)}
.auth{max-width:410px;margin:8vh auto}
.auth-hero{text-align:center;margin-bottom:1.2rem}
.auth-hero .ring{width:60px;height:60px;margin:0 auto .7rem;border-radius:14px;display:grid;place-items:center;font-size:1.5rem;
  background:var(--surface2);border:1px solid var(--accent-line)}
input,textarea,select{width:100%;padding:.7rem .85rem;background:var(--surface2);border:1px solid var(--line);border-radius:9px;color:var(--fg);font-family:inherit;font-size:.92rem;outline:none;transition:.15s}
input:focus,textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}
label{font-size:11px;color:var(--muted);display:block;margin:.7rem 0 .3rem;text-transform:uppercase;letter-spacing:.06em}
.btn{display:inline-block;background:var(--accent);color:#fff;border:0;border-radius:9px;padding:.62rem 1.1rem;font-weight:600;cursor:pointer;text-align:center;transition:.16s;font-size:.9rem}
.btn:hover{background:#5f98ff;color:#fff}
.btn.full{display:block;width:100%}
.btn.ghost{background:transparent;color:var(--fg);border:1px solid var(--line2)}.btn.ghost:hover{background:var(--raise)}
.note{background:rgba(242,86,75,.1);border:1px solid rgba(242,86,75,.35);border-radius:9px;padding:.7rem 1rem;margin:.7rem 0;color:#f3a09a;font-size:.88rem}
.stat{display:flex;gap:12px;flex-wrap:wrap;margin-top:1.2rem}
.stat .b{background:var(--surface);border:1px solid var(--line);border-radius:11px;padding:.75rem 1.05rem;min-width:96px}
.stat .b b{font-size:1.4rem;display:block;font-weight:600;letter-spacing:-.02em}.stat .b span{color:var(--dim);font-size:11px;letter-spacing:.05em;text-transform:uppercase}
footer{border-top:1px solid var(--line);margin-top:2.5rem;padding:1.4rem 26px;color:var(--dim);text-align:center;font-size:.8rem}
.sfbtn{background:var(--accent);color:#fff;border:0;border-radius:8px;padding:.45rem .95rem;font-weight:600;cursor:pointer;font-size:.83rem;transition:.16s}
.sfbtn:hover{background:#5f98ff}
.fmodal{display:none;position:fixed;inset:0;z-index:60;background:rgba(3,6,15,.72);backdrop-filter:blur(4px);align-items:center;justify-content:center}
.fmodal.on{display:flex}
.fbox{width:min(430px,92vw);background:var(--surface);border:1px solid var(--line2);border-radius:14px;padding:1.3rem;box-shadow:0 30px 80px -20px rgba(0,0,0,.8)}
.fbox input{font-family:var(--mono)}
.toastbox{position:fixed;right:18px;bottom:18px;z-index:70;display:flex;flex-direction:column;gap:.5rem}
.toast{background:var(--surface);border:1px solid var(--line2);border-left:3px solid var(--green);border-radius:11px;padding:.75rem 1rem;min-width:240px;animation:fadeUp .3s both;font-size:.86rem}
@media(max-width:860px){.shell{grid-template-columns:1fr}.rail{position:static;height:auto;flex-direction:row;flex-wrap:wrap;align-items:center;gap:10px}
  .rail .railcap,.rail .railfoot .lvlseg{display:none}.railnav{flex-direction:row;flex-wrap:wrap}.railfoot{margin:0}main{padding:0 16px}}
'; }

function bg_html(): string { return ''; }
function particles_js(): string { return ''; }

function logomark(int $sz = 28): string {
    return '<svg viewBox="0 0 40 40" width="'.$sz.'" height="'.$sz.'" fill="none">
<defs><linearGradient id="lg" x1="0" y1="0" x2="40" y2="40"><stop stop-color="#4f8cff"/><stop offset="1" stop-color="#7c5cff"/></linearGradient></defs>
<path d="M20 3 L34 11 V28 L20 37 L6 28 V11 Z" stroke="url(#lg)" stroke-width="2.4"/>
<path d="M22 9 L13 23 H19 L17 31 L27 16 H21 Z" fill="url(#lg)"/></svg>';
}
function brand(string $size = '15px'): string {
    return '<span class="brand">'.logomark(26).'<b style="font-size:'.$size.'">Volt<span class="gradtext">Verse</span></b></span>';
}

function nav_items(): array {
    $items = [
      ['Dashboard','/dashboard.php','<path d="M4 4h6v7H4zM14 4h6v4h-6zM14 12h6v8h-6zM4 15h6v5H4z"/>'],
      ['Challenges','/challenges.php','<path d="M4 6h16M4 12h16M4 18h11"/>'],
      ['Campaigns','/campaigns.php','<path d="M6 3v6l6 3 6-3V3M6 21v-6l6-3 6 3v6"/>'],
      ['Leaderboard','/leaderboard.php','<path d="M5 20V11M12 20V4M19 20v-6M3 20h18"/>'],
      ['SOC','/soc.php','<path d="M12 3l8 4v5c0 5-3.4 7.6-8 9-4.6-1.4-8-4-8-9V7z"/>'],
      ['Profile','/profile.php','<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>'],
    ];
    if (is_admin_user()) $items[] = ['Admin','/admin.php','<path d="M3 7l9-4 9 4-9 4z"/><path d="M7 10v5c0 2 10 2 10 0v-5"/>'];
    return $items;
}

function head(string $title, $app = null): void {
    $u = pf_user(); $lv = level();
    $cur = basename($_SERVER['SCRIPT_NAME'] ?? '');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . ' · VoltVerse</title>
<style>' . css() . '</style></head><body>';
    if ($u) {
        $ini = strtoupper(substr(preg_replace('/[^A-Za-z]/','',$u['name']).'X',0,2));
        echo '<div class="shell"><aside class="rail">
          <a href="/dashboard.php" style="text-decoration:none;display:block">' . brand() . '</a>
          <div><div class="railcap">Workspace</div><nav class="railnav">';
        foreach (nav_items() as [$label,$href,$path]) {
            $on = basename($href) === $cur ? ' class="on"' : '';
            echo '<a'.$on.' href="'.$href.'"><svg viewBox="0 0 24 24">'.$path.'</svg>'.$label.'</a>';
        }
        echo '</nav></div><div class="railfoot"><div class="railcap">Difficulty</div><div class="lvlseg">';
        foreach (['low','medium','high','secure'] as $L)
            echo '<a href="/level.php?set='.$L.'" class="'.($lv===$L?'on '.$L:'').'">'.ucfirst(substr($L,0,3)).'</a>';
        echo '</div><div class="mecard"><span class="ava">'.e($ini).'</span>
          <div class="nm"><b>'.e($u['name']).'</b><small>Operator</small></div>
          <a href="/logout.php" title="Sign out"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg></a>
          </div></div></aside><div class="workarea">
          <div class="topbar"><div class="pt">' . ($app ? e($app['name']) : e($title)) . ($app ? '<small>Target application</small>' : '') . '</div>
            <div class="sp"></div>
            <a href="/level.php"><span class="lvlpill lvl-'.$lv.'">'.strtoupper($lv).'</span></a>
            <button class="sfbtn" onclick="openFlag()">Submit flag</button></div>
          <main>';
        echo '<div id="fmodal" class="fmodal"><div class="fbox"><div style="display:flex;justify-content:space-between;align-items:center">
             <h3 style="margin:0;font-size:1.05rem">Submit a flag</h3><span onclick="closeFlag()" style="cursor:pointer;color:var(--muted)">✕</span></div>
             <p style="color:var(--muted);font-size:.85rem">Found a flag? Paste it to earn points.</p>
             <input id="fin" placeholder="VOLT{...}" onkeydown="if(event.key===\'Enter\')subFlag()">
             <button class="btn full" style="margin-top:.7rem" onclick="subFlag()">Submit</button>
             <div id="fres" style="margin-top:.7rem"></div></div></div>';
    } else {
        echo '<main>';
    }
}

function flag_js(): string { return "<div class='toastbox' id='toasts'></div><script>
function toast(m){var b=document.getElementById('toasts');var t=document.createElement('div');t.className='toast';t.innerHTML=m;b.appendChild(t);setTimeout(function(){t.remove()},4200);}
function openFlag(){document.getElementById('fmodal').classList.add('on');setTimeout(function(){document.getElementById('fin').focus()},50);}
function closeFlag(){document.getElementById('fmodal').classList.remove('on');}
document.addEventListener('click',function(e){if(e.target.id==='fmodal')closeFlag();});
document.addEventListener('keydown',function(e){if((e.metaKey||e.ctrlKey)&&e.key==='k'){e.preventDefault();openFlag();}});
async function subFlag(){var v=document.getElementById('fin').value.trim();if(!v)return;
 var r=await fetch('/flag.php',{method:'POST',headers:{'content-type':'application/json'},body:JSON.stringify({flag:v})});
 var d=await r.json();var res=document.getElementById('fres');
 if(d.correct){var extra=d.already?' (already solved)':(d.first_blood?' · First blood':'');
   res.innerHTML='<div style=\"color:#7ee0a0\">Solved '+d.title+' — +'+d.points+' pts'+extra+'</div>';
   toast('Solved <b>'+d.title+'</b> +'+d.points+' pts · '+d.solved+'/'+d.total);
   document.getElementById('fin').value='';setTimeout(function(){location.reload()},1100);}
 else res.innerHTML='<div style=\"color:#f3a09a\">'+(d.msg||'Incorrect flag')+'</div>';}
</script>"; }

function foot(): void {
    $u = pf_user();
    if ($u) {
        echo '</main><footer>' . brand('13px') . ' &nbsp;·&nbsp; security training range — for authorized, local use only</footer>'
           . flag_js() . '</div></div></body></html>';
    } else {
        echo '</main></body></html>';
    }
}

function stars($r): string { $f=(int)round((float)$r); return str_repeat('★',$f).str_repeat('☆',5-$f); }
