<?php
require_once __DIR__ . '/../inc/layout.php';
require_login();
$APP = ['ico'=>'🔌','name'=>'Voltmart REST API'];
head('REST API', $APP);
$eps = [
  ['GET','/api/v1.php/users','List all users','API3 · Excessive Data Exposure — leaks passwords'],
  ['GET','/api/v1.php/users/2','Get a user by id','API1 · BOLA — read ANY user (try id 2)'],
  ['POST','/api/v1.php/users','Create a user','API6 · Mass Assignment — send {"is_admin":1}'],
  ['DELETE','/api/v1.php/users/2','Delete a user','API5 · BFLA — admin-only function, no role check'],
  ['GET','/api/v1.php/orders/1002','Get an order','API1 · BOLA on orders'],
];
$mc = ['GET'=>'#22d3ee','POST'=>'#34d399','DELETE'=>'#f87171','PUT'=>'#fbbf24'];
?>
<div style="margin-bottom:1rem"><a href="/dashboard.php">← Apps</a></div>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">🔌 Voltmart API · v1</span>
  <h1 style="font-size:2rem">REST API reference</h1>
  <p>A JSON API for the Voltmart platform. Test it below or with Burp / Postman. Endpoints are intentionally vulnerable (OWASP API Top 10) at Low difficulty.</p>
</div>

<div class="aiwrap" style="display:grid;grid-template-columns:1fr 380px;gap:1.2rem;margin-top:1.4rem">
  <div>
    <h2>Endpoints</h2>
    <?php foreach ($eps as $i=>[$m,$p,$d,$vuln]): ?>
      <div class="panel" style="padding:1rem 1.2rem;cursor:pointer" onclick="load('<?= $m ?>','<?= $p ?>')">
        <div style="display:flex;align-items:center;gap:.7rem">
          <span style="font-weight:800;color:<?= $mc[$m] ?>;min-width:56px"><?= $m ?></span>
          <code style="color:var(--fg)"><?= e($p) ?></code></div>
        <div style="color:var(--muted);font-size:.85rem;margin-top:.4rem"><?= e($d) ?> · <span style="color:#a78bfa"><?= e($vuln) ?></span></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="panel" style="position:sticky;top:80px">
    <h3 style="margin-top:0">▶ Try it</h3>
    <div style="display:flex;gap:.5rem"><select id="m" style="width:auto;background:rgba(255,255,255,.05);border:1px solid var(--line);color:var(--fg);border-radius:10px;padding:.5rem">
      <option>GET</option><option>POST</option><option>DELETE</option></select>
      <input id="p" value="/api/v1.php/users/2" style="flex:1;background:rgba(255,255,255,.05);border:1px solid var(--line);color:var(--fg);border-radius:10px;padding:.5rem;font-family:ui-monospace,monospace"></div>
    <label style="display:block;font-size:.72rem;color:var(--muted);margin:.7rem 0 .2rem">Body (JSON, for POST)</label>
    <textarea id="bd" rows="2" placeholder='{"email":"x@y.com","is_admin":1}' style="width:100%;background:rgba(255,255,255,.05);border:1px solid var(--line);color:var(--fg);border-radius:10px;padding:.5rem;font-family:ui-monospace,monospace;font-size:.82rem"></textarea>
    <button class="btn full" style="margin-top:.7rem" onclick="send()">Send request</button>
    <label style="display:block;font-size:.72rem;color:var(--muted);margin:.8rem 0 .2rem">Response</label>
    <pre id="out" style="background:#05080f;border:1px solid var(--line);border-radius:10px;padding:.8rem;max-height:340px;overflow:auto;font-size:.8rem;color:#93c5fd;white-space:pre-wrap">// response appears here</pre>
  </div>
</div>
<script>
function load(m,p){document.getElementById('m').value=m;document.getElementById('p').value=p;
  document.getElementById('bd').value=(m==='POST')?'{"email":"hacker@volt.local","name":"H","is_admin":1}':'';}
async function send(){
  var m=document.getElementById('m').value,p=document.getElementById('p').value,bd=document.getElementById('bd').value.trim();
  var opt={method:m,headers:{'content-type':'application/json'}};if(m!=='GET'&&bd)opt.body=bd;
  try{var r=await fetch(p,opt);var t=await r.text();
    document.getElementById('out').textContent=r.status+' '+r.statusText+'\n\n'+t;
    if(t.indexOf('VOLT{')>-1)toast('🚩 Flag found in the API response! Copy it and Submit flag.');
  }catch(e){document.getElementById('out').textContent='Error: '+e;}}
</script>
<?php foot();
