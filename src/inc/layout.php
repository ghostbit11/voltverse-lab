<?php
require_once __DIR__ . '/core.php';

function css(): string { return '
:root{--bg:#05070f;--fg:#e8eefc;--muted:#93a4c6;--dim:#5b6d92;--line:rgba(255,255,255,.08);
--cyan:#22d3ee;--accent2:#22d3ee;--blue:#3b82f6;--purple:#a78bfa;--green:#34d399;--amber:#fbbf24;--red:#f87171;
--glass:rgba(15,22,44,.55);--grad:linear-gradient(135deg,#3b82f6,#22d3ee)}
*{box-sizing:border-box}
html,body{margin:0;min-height:100%}
body{font-family:Inter,"Segoe UI",system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--fg);
overflow-x:hidden;-webkit-font-smoothing:antialiased}
a{color:var(--cyan);text-decoration:none}a:hover{color:#67e8f9}
/* animated background layers */
#bg{position:fixed;inset:0;z-index:-3}
.gridbg{position:fixed;inset:0;z-index:-2;pointer-events:none;
background:linear-gradient(rgba(59,130,246,.05) 1px,transparent 1px) 0 0/44px 44px,
linear-gradient(90deg,rgba(59,130,246,.05) 1px,transparent 1px) 0 0/44px 44px;
-webkit-mask:radial-gradient(circle at 50% 0,#000,transparent 75%);mask:radial-gradient(circle at 50% 0,#000,transparent 75%)}
.orb{position:fixed;border-radius:50%;filter:blur(90px);opacity:.55;z-index:-1;animation:float 14s ease-in-out infinite}
.o1{width:520px;height:520px;background:radial-gradient(circle,#3b82f6,transparent 70%);top:-160px;right:-120px}
.o2{width:460px;height:460px;background:radial-gradient(circle,#22d3ee,transparent 70%);bottom:-180px;left:-120px;animation-delay:-4s}
.o3{width:380px;height:380px;background:radial-gradient(circle,#a78bfa,transparent 70%);top:40%;left:55%;animation-delay:-8s;opacity:.4}
@keyframes float{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(20px,-30px) scale(1.08)}}
@keyframes grad{to{background-position:300% center}}
@keyframes fadeUp{from{opacity:0;transform:translateY(26px)}to{opacity:1;transform:none}}
@keyframes glow{0%,100%{box-shadow:0 0 20px rgba(34,211,238,.35)}50%{box-shadow:0 0 42px rgba(34,211,238,.7)}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes pulse{0%,100%{opacity:.5}50%{opacity:1}}
.gradtext{background:linear-gradient(90deg,#22d3ee,#a78bfa,#3b82f6,#22d3ee);background-size:300% auto;
-webkit-background-clip:text;background-clip:text;color:transparent;animation:grad 7s linear infinite}
.fadeup{animation:fadeUp .8s cubic-bezier(.2,.7,.3,1) both}
.d1{animation-delay:.08s}.d2{animation-delay:.18s}.d3{animation-delay:.3s}.d4{animation-delay:.42s}
/* nav */
.nav{position:sticky;top:0;z-index:20;display:flex;align-items:center;gap:1.2rem;padding:.9rem 1.6rem;
background:rgba(5,7,15,.6);backdrop-filter:blur(16px);border-bottom:1px solid var(--line)}
.logo{font-size:1.35rem;font-weight:900;letter-spacing:-.5px;display:flex;align-items:center;gap:.5rem}
.logo .z{filter:drop-shadow(0 0 12px var(--cyan));animation:pulse 3s ease-in-out infinite}
.nav .app{color:var(--muted);border-left:1px solid var(--line);padding-left:1.2rem;font-weight:600}
.nav form{flex:1;display:flex;max-width:440px;background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:12px;overflow:hidden}
.nav input[type=search]{flex:1;border:0;background:transparent;color:var(--fg);padding:.6rem .9rem;outline:none}
.nav .go{border:0;background:var(--grad);color:#04121f;font-weight:800;padding:0 1.1rem;cursor:pointer}
.nav .sp{margin-left:auto}.nav .links{display:flex;gap:1rem;align-items:center;font-size:.9rem}.nav .links a{color:var(--muted)}.nav .links a:hover{color:#fff}
.lvlpill{font-size:.72rem;font-weight:800;padding:3px 12px;border-radius:999px;border:1px solid}
.lvl-low{color:#fca5a5;border-color:rgba(248,113,113,.5);background:rgba(248,113,113,.1)}
.lvl-medium{color:#fcd34d;border-color:rgba(251,191,36,.5);background:rgba(251,191,36,.1)}
.lvl-high{color:#fdba74;border-color:rgba(249,115,22,.5);background:rgba(249,115,22,.1)}
.lvl-secure{color:#6ee7b7;border-color:rgba(52,211,153,.5);background:rgba(52,211,153,.1)}
main{max-width:1380px;margin:1.6rem auto;padding:0 2rem;position:relative;z-index:1}
.logo svg{display:block}
/* hero */
.hero{position:relative;overflow:hidden;border:1px solid var(--line);border-radius:26px;padding:3rem 2.6rem;
background:linear-gradient(135deg,rgba(59,130,246,.14),rgba(34,211,238,.06)),var(--glass);backdrop-filter:blur(14px)}
.hero .eyebrow{display:inline-flex;align-items:center;gap:.5rem;color:var(--cyan);font-weight:700;letter-spacing:2px;
font-size:.72rem;text-transform:uppercase;border:1px solid rgba(34,211,238,.3);border-radius:999px;padding:5px 14px;background:rgba(34,211,238,.06)}
.hero h1{margin:.9rem 0 .4rem;font-size:3rem;font-weight:900;letter-spacing:-1.5px;line-height:1.03;max-width:720px}
.hero p{color:var(--muted);max-width:560px;font-size:1.05rem}
.cta{display:inline-flex;align-items:center;gap:.5rem;margin-top:1.3rem;background:var(--grad);color:#04121f;font-weight:800;
padding:.8rem 1.5rem;border-radius:14px;animation:glow 3s ease-in-out infinite;transition:.2s}
.cta:hover{transform:translateY(-2px);color:#04121f}
.shield{position:absolute;right:2.4rem;top:50%;transform:translateY(-50%);font-size:8rem;
filter:drop-shadow(0 0 40px rgba(34,211,238,.55));animation:float 8s ease-in-out infinite}
/* app cards */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:1.2rem}
.appcard{display:block;position:relative;border:1px solid var(--line);border-radius:20px;padding:1.5rem;overflow:hidden;
background:var(--glass);backdrop-filter:blur(14px);color:var(--fg);transition:.25s cubic-bezier(.2,.7,.3,1)}
.appcard::before{content:"";position:absolute;inset:0;border-radius:20px;padding:1px;background:linear-gradient(135deg,rgba(34,211,238,.5),transparent 40%);
-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:0;transition:.25s}
.appcard:hover{transform:translateY(-6px);text-decoration:none;box-shadow:0 24px 60px rgba(0,0,0,.5),0 0 40px rgba(34,211,238,.12)}
.appcard:hover::before{opacity:1}
.appcard .ico{font-size:2.8rem;filter:drop-shadow(0 0 18px rgba(59,130,246,.5))}
.appcard h3{margin:.6rem 0 .3rem;font-size:1.15rem;color:#fff}.appcard p{color:var(--muted);font-size:.85rem;min-height:2.6em}
.tags{display:flex;gap:.35rem;flex-wrap:wrap;margin-top:.6rem}
.tag{font-size:.66rem;font-weight:700;color:var(--cyan);background:rgba(34,211,238,.1);border:1px solid rgba(34,211,238,.3);border-radius:6px;padding:2px 8px}
.soon{opacity:.5}.soon .tag{color:var(--dim);background:rgba(255,255,255,.03);border-color:var(--line)}
.live{position:absolute;top:1.2rem;right:1.2rem;font-size:.65rem;font-weight:800;color:var(--green);display:flex;align-items:center;gap:5px}
.live::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--green);box-shadow:0 0 8px var(--green);animation:pulse 1.6s infinite}
/* panels / auth */
.panel{border:1px solid var(--line);border-radius:20px;padding:1.6rem;margin-bottom:1.2rem;background:var(--glass);backdrop-filter:blur(14px)}
.auth{max-width:430px;margin:6vh auto}
.auth-hero{text-align:center;margin-bottom:1.2rem}
.auth-hero .ring{width:74px;height:74px;margin:0 auto .6rem;border-radius:50%;display:grid;place-items:center;font-size:2rem;
background:radial-gradient(circle,rgba(34,211,238,.2),transparent 70%);border:1px solid rgba(34,211,238,.3);animation:glow 3s ease-in-out infinite}
input,textarea,select{width:100%;padding:.75rem .9rem;background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:12px;color:var(--fg);font-family:inherit;font-size:.95rem;outline:none;transition:.15s}
input:focus,textarea:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(34,211,238,.12)}
label{font-size:.76rem;color:var(--muted);display:block;margin:.7rem 0 .3rem;text-transform:uppercase;letter-spacing:.5px}
.btn{display:inline-block;background:var(--grad);color:#04121f;border:0;border-radius:12px;padding:.75rem 1.2rem;font-weight:800;cursor:pointer;text-align:center;transition:.2s}
.btn:hover{transform:translateY(-2px);box-shadow:0 0 30px rgba(34,211,238,.5);color:#04121f}
.btn.full{display:block;width:100%}.btn.ghost{background:rgba(255,255,255,.05);color:#fff;border:1px solid var(--line)}
.note{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.35);border-radius:12px;padding:.7rem 1rem;margin:.7rem 0;color:#fca5a5;font-size:.9rem}
.stat{display:flex;gap:1rem;flex-wrap:wrap;margin-top:1.3rem}
.stat .b{background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:12px;padding:.7rem 1.1rem}
.stat .b b{font-size:1.3rem;display:block}.stat .b span{color:var(--muted);font-size:.75rem}
footer{border-top:1px solid var(--line);margin-top:3rem;padding:1.6rem;color:var(--dim);text-align:center;font-size:.82rem;position:relative;z-index:1}
.sfbtn{background:linear-gradient(135deg,#f59e0b,#f97316);color:#0b1220;border:0;border-radius:10px;padding:.42rem .9rem;font-weight:800;cursor:pointer;font-size:.82rem}
.sfbtn:hover{filter:brightness(1.08)}
.fmodal{display:none;position:fixed;inset:0;z-index:60;background:rgba(3,6,15,.7);backdrop-filter:blur(4px);align-items:center;justify-content:center}
.fmodal.on{display:flex}
.fbox{width:min(440px,92vw);background:var(--glass);backdrop-filter:blur(18px);border:1px solid var(--line);border-radius:18px;padding:1.4rem;box-shadow:0 30px 80px rgba(0,0,0,.6)}
.fbox input{width:100%;background:rgba(255,255,255,.05);border:1px solid var(--line);border-radius:10px;color:var(--fg);padding:.7rem .9rem;outline:none;font-family:ui-monospace,monospace}
.toastbox{position:fixed;right:18px;bottom:18px;z-index:70;display:flex;flex-direction:column;gap:.5rem}
.toast{background:var(--glass);backdrop-filter:blur(14px);border:1px solid var(--line);border-left:3px solid var(--green);border-radius:12px;padding:.8rem 1.1rem;min-width:240px;animation:fadeUp .3s both}
'; }

function bg_html(): string {
    return '<canvas id="bg"></canvas><div class="gridbg"></div><div class="orb o1"></div><div class="orb o2"></div><div class="orb o3"></div>';
}
function particles_js(): string { return "<script>
(function(){var c=document.getElementById('bg');if(!c)return;var x=c.getContext('2d'),w,h,P=[];
function rs(){w=c.width=innerWidth;h=c.height=innerHeight;}rs();addEventListener('resize',rs);
for(var i=0;i<70;i++)P.push({x:Math.random()*w,y:Math.random()*h,vx:(Math.random()-.5)*.4,vy:(Math.random()-.5)*.4});
function loop(){x.clearRect(0,0,w,h);for(var i=0;i<P.length;i++){var p=P[i];p.x+=p.vx;p.y+=p.vy;
if(p.x<0||p.x>w)p.vx*=-1;if(p.y<0||p.y>h)p.vy*=-1;x.fillStyle='rgba(34,211,238,.7)';x.beginPath();x.arc(p.x,p.y,1.6,0,7);x.fill();
for(var j=i+1;j<P.length;j++){var q=P[j],dx=p.x-q.x,dy=p.y-q.y,d=dx*dx+dy*dy;if(d<12000){x.strokeStyle='rgba(59,130,246,'+(1-d/12000)*.28+')';x.lineWidth=1;x.beginPath();x.moveTo(p.x,p.y);x.lineTo(q.x,q.y);x.stroke();}}}
requestAnimationFrame(loop);}loop();})();
</script>"; }

function logomark(int $sz = 30): string {
    return '<svg viewBox="0 0 40 40" width="'.$sz.'" height="'.$sz.'" fill="none" style="filter:drop-shadow(0 0 8px rgba(34,211,238,.6))">
<defs><linearGradient id="lg" x1="0" y1="0" x2="40" y2="40"><stop stop-color="#3b82f6"/><stop offset="1" stop-color="#22d3ee"/></linearGradient></defs>
<path d="M20 2 L35 11 V29 L20 38 L5 29 V11 Z" stroke="url(#lg)" stroke-width="2.5"/>
<path d="M22 8 L12 23 H19 L17 32 L28 16 H21 Z" fill="url(#lg)"/></svg>';
}
function brand(string $size = '1.35rem'): string {
    return '<span class="logo" style="font-size:'.$size.'">'.logomark(28).'<span style="letter-spacing:-.5px"><b>BREACH</b><span class="gradtext">R</span></span></span>';
}

function head(string $title, $app = null): void {
    $u = pf_user(); $lv = level();
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . ' · BREACHR</title>
<style>' . css() . '</style></head><body>' . bg_html();
    if ($u) {
        echo '<div class="nav"><a class="logo" href="/dashboard.php">' . brand() . '</a>';
        if ($app) echo '<span class="app">' . e($app['ico'] . ' ' . $app['name']) . '</span>';
        echo '<div class="links sp">'
          . ($app ? '<a href="/dashboard.php">Apps</a>' : '')
          . '<a href="/challenges.php">Challenges</a><a href="/campaigns.php">Campaigns</a><a href="/leaderboard.php">Leaderboard</a><a href="/soc.php">🛡 SOC</a>'
          . (is_instructor() ? '<a href="/instructor.php">🎓 Instructor</a>' : '')
          . '<a href="/level.php"><span class="lvlpill lvl-' . $lv . '">' . strtoupper($lv) . '</span></a>'
          . '<button class="sfbtn" onclick="openFlag()">🚩 Submit flag</button>'
          . '<a href="/profile.php">Profile</a>'
          . '<a href="/logout.php">Sign out</a></div></div>'
          . '<div id="fmodal" class="fmodal"><div class="fbox"><div style="display:flex;justify-content:space-between;align-items:center">
             <h3 style="margin:0">🚩 Submit a flag</h3><span onclick="closeFlag()" style="cursor:pointer;color:var(--muted)">✕</span></div>
             <p style="color:var(--muted);font-size:.85rem">Found a flag? Paste it to earn points.</p>
             <input id="fin" placeholder="VOLT{...}" onkeydown="if(event.key===\'Enter\')subFlag()">
             <button class="btn full" style="margin-top:.7rem" onclick="subFlag()">Submit</button>
             <div id="fres" style="margin-top:.7rem"></div></div></div>';
    }
    echo '<main>';
}

function flag_js(): string { return "<div class='toastbox' id='toasts'></div><script>
function toast(m){var b=document.getElementById('toasts');var t=document.createElement('div');t.className='toast';t.innerHTML=m;b.appendChild(t);setTimeout(function(){t.remove()},4200);}
function openFlag(){document.getElementById('fmodal').classList.add('on');setTimeout(function(){document.getElementById('fin').focus()},50);}
function closeFlag(){document.getElementById('fmodal').classList.remove('on');}
document.addEventListener('click',function(e){if(e.target.id==='fmodal')closeFlag();});
async function subFlag(){var v=document.getElementById('fin').value.trim();if(!v)return;
 var r=await fetch('/flag.php',{method:'POST',headers:{'content-type':'application/json'},body:JSON.stringify({flag:v})});
 var d=await r.json();var res=document.getElementById('fres');
 if(d.correct){var extra=d.already?' (already solved)':(d.first_blood?' · 🩸 First blood!':'');
   res.innerHTML='<div style=\"color:#6ee7b7\">✅ '+d.title+' — +'+d.points+' pts'+extra+'</div>';
   toast('✅ Solved <b>'+d.title+'</b> +'+d.points+' pts · '+d.solved+'/'+d.total);
   document.getElementById('fin').value='';setTimeout(function(){location.reload()},1200);}
 else res.innerHTML='<div style=\"color:#fca5a5\">✗ '+(d.msg||'Incorrect flag')+'</div>';}
</script>"; }
function foot(): void {
    $extra = pf_user() ? flag_js() : '';
    echo '</main><footer>' . brand('.95rem') . ' — cybersecurity training range · Web · API · AI · LLM</footer>' . $extra . particles_js() . '</body></html>';
}

function stars($r): string { $f=(int)round((float)$r); return str_repeat('★',$f).str_repeat('☆',5-$f); }
