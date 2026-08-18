<?php
require_once __DIR__ . '/../inc/layout.php';
require_login();
$APP = ['ico'=>'📡','name'=>'VoltData (GraphQL API)'];
head('VoltData', $APP);
?>
<div style="margin-bottom:1rem"><a href="/dashboard.php">← Apps</a></div>
<div class="hero fadeup" style="padding:2rem 2.4rem">
  <span class="eyebrow">📡 VoltData · GraphQL</span>
  <h1 style="font-size:2rem">GraphQL explorer</h1>
  <p>The internal <code>/graphql/api.php</code> endpoint powers VoltVerse's data layer. Send a query and inspect the response.</p>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;margin-top:1.4rem">
  <div class="panel">
    <h3 style="margin-top:0">Query</h3>
    <textarea id="q" rows="10" style="width:100%;background:rgba(255,255,255,.05);border:1px solid var(--line);color:#93c5fd;border-radius:10px;padding:.7rem;font-family:ui-monospace,monospace;font-size:.82rem">{ me { id email } }</textarea>
    <button class="btn full" style="margin-top:.6rem" onclick="run()">▶ Run query</button>
    <div class="note" style="margin-top:.8rem;color:var(--muted);font-size:.82rem">Fields: <code>me</code>, <code>user(id: N)</code>, <code>products(filter: "…")</code>.</div>
  </div>
  <div class="panel">
    <h3 style="margin-top:0">Response</h3>
    <pre id="out" style="background:rgba(255,255,255,.04);border:1px solid var(--line);color:#a7f3d0;border-radius:10px;padding:.8rem;min-height:220px;overflow:auto;font-size:.8rem;white-space:pre-wrap">// response appears here</pre>
  </div>
</div>
<script>
async function run(){
  var out=document.getElementById('out');out.textContent='…';
  try{ var r=await fetch('/graphql/api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({query:document.getElementById('q').value})});
    var t=await r.text(); out.textContent=t;
    var m=t.match(/VOLT\{[^}]+\}/); if(m&&typeof toast==='function')toast('Flag found: '+m[0]);
  }catch(e){ out.textContent='Error: '+e; }
}
</script>
<?php foot();
