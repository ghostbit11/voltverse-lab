<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/hints.php';
require_login();
$me = pf_user();
if (!is_admin_user()) {
    head('Admin'); echo '<div class="panel"><h2>Admins only</h2><p style="color:var(--muted)">This workspace console is available to your organisation\'s admin. You are signed in as a member.</p><a class="btn" href="/dashboard.php">Back to dashboard</a></div>'; foot(); exit;
}
ensure_solves(); ensure_hint_unlocks(); ensure_assignments(); ensure_roles();
$all = CATALOG(); $total = count($all);
$note = null; $noteBad = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    if ($act === 'settings') {
        set_setting('walkthroughs_enabled', isset($_POST['walkthroughs']) ? '1' : '0');
        set_setting('hints_enabled', isset($_POST['hints']) ? '1' : '0');
        set_setting('open_signup', isset($_POST['open_signup']) ? '1' : '0');
        $note = 'Workspace settings saved.';
    } elseif ($act === 'create_user') {
        $em = trim($_POST['email'] ?? ''); $nm = trim($_POST['name'] ?? ''); $pw = $_POST['password'] ?? '';
        $rl = ($_POST['role'] ?? 'member') === 'admin' ? 'admin' : 'member';
        if (!$em || !$pw) { $note = 'Email and password are required.'; $noteBad = true; }
        else { try { create_platform_user($em, $nm, $pw, $rl); $note = "Created $rl account for $em."; }
               catch (Throwable $e) { $note = 'That email is already registered.'; $noteBad = true; } }
    } elseif ($act === 'reset' && !empty($_POST['email'])) {
        $em = $_POST['email'];
        db()->prepare("DELETE FROM solves WHERE player=?")->execute([$em]);
        db()->prepare("DELETE FROM hint_unlocks WHERE player=?")->execute([$em]);
        $note = "Progress reset for $em.";
    } elseif ($act === 'role' && !empty($_POST['email'])) {
        $em = $_POST['email'];
        if ($em === $me['email']) { $note = "You can't change your own role."; $noteBad = true; }
        else { db()->prepare("UPDATE platform_users SET role=? WHERE email=?")->execute([$_POST['to']==='admin'?'admin':'member', $em]); $note = "Role updated for $em."; }
    } elseif ($act === 'delete' && !empty($_POST['email'])) {
        $em = $_POST['email'];
        if ($em === $me['email']) { $note = "You can't delete your own account."; $noteBad = true; }
        else { foreach (['platform_users','solves','hint_unlocks','assignments'] as $t) {
                 $col = $t==='platform_users' ? 'email' : 'player';
                 db()->prepare("DELETE FROM $t WHERE $col=?")->execute([$em]); }
               $note = "Deleted $em and all their data."; }
    } elseif ($act === 'assign' && !empty($_POST['player'])) {
        $em = $_POST['player']; $picked = $_POST['cid'] ?? [];
        foreach ($all as $c) set_assignment($em, $c[0], in_array($c[0], $picked, true));
        $note = 'Assignments updated for ' . $em . ' (' . count($picked) . ' tests).';
    }
}

$wtOn = setting('walkthroughs_enabled','1')==='1';
$hintsOn = setting('hints_enabled','1')==='1';
$openOn = setting('open_signup','1')==='1';
$players = db()->query("SELECT id,email,name,role FROM platform_users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$totSolves = (int)db()->query("SELECT COUNT(*) FROM solves")->fetchColumn();
$active = (int)db()->query("SELECT COUNT(DISTINCT player) FROM solves")->fetchColumn();
$assignFor = $_GET['assign'] ?? '';

head('Admin');
?>
<div class="hero fadeup" style="padding:1.7rem 1.9rem">
  <span class="eyebrow">Workspace · admin console</span>
  <h1 style="font-size:1.7rem">Team &amp; content</h1>
  <p>Add employees, assign tests, control learning aids, and track everyone's progress across the range.</p>
</div>
<?php if ($note): ?><div class="panel" style="margin-top:1rem;border-color:<?= $noteBad?'rgba(242,86,75,.4)':'var(--accent-line)' ?>;color:<?= $noteBad?'#f3a09a':'#9db8ff' ?>"><?= e($note) ?></div><?php endif; ?>

<div class="stat" style="margin-top:1.2rem">
  <div class="b"><b class="gradtext"><?= count($players) ?></b><span>Users</span></div>
  <div class="b"><b class="gradtext"><?= $active ?></b><span>Active</span></div>
  <div class="b"><b class="gradtext"><?= $totSolves ?></b><span>Total solves</span></div>
  <div class="b"><b class="gradtext"><?= $total ?></b><span>Challenges</span></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1.4rem">
  <div class="panel" style="margin:0">
    <h3 style="margin-top:0">Content controls</h3>
    <p style="color:var(--muted);margin-top:0;font-size:.88rem">What members can see. You always see everything.</p>
    <form method="post"><input type="hidden" name="action" value="settings">
      <label style="display:flex;align-items:center;gap:.6rem;text-transform:none;letter-spacing:0;margin:.5rem 0"><input type="checkbox" name="walkthroughs" style="width:18px" <?= $wtOn?'checked':'' ?>> Show <b>walkthroughs</b> to members</label>
      <label style="display:flex;align-items:center;gap:.6rem;text-transform:none;letter-spacing:0;margin:.5rem 0"><input type="checkbox" name="hints" style="width:18px" <?= $hintsOn?'checked':'' ?>> Show <b>hints</b> to members</label>
      <label style="display:flex;align-items:center;gap:.6rem;text-transform:none;letter-spacing:0;margin:.5rem 0"><input type="checkbox" name="open_signup" style="width:18px" <?= $openOn?'checked':'' ?>> Allow <b>open self-registration</b> <span style="color:var(--dim)">(off = only you add users)</span></label>
      <button class="btn" style="margin-top:.6rem">Save settings</button>
    </form>
  </div>
  <div class="panel" style="margin:0">
    <h3 style="margin-top:0">Add an employee</h3>
    <form method="post"><input type="hidden" name="action" value="create_user">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem">
        <div><label>Name</label><input name="name" placeholder="Jane Doe"></div>
        <div><label>Role</label><select name="role"><option value="member">Member</option><option value="admin">Admin</option></select></div>
      </div>
      <label>Email</label><input name="email" type="email" placeholder="jane@company.com" required>
      <label>Temporary password</label><input name="password" required placeholder="they can use this to sign in">
      <button class="btn full" style="margin-top:.8rem">Create account</button>
    </form>
  </div>
</div>

<div class="panel" style="margin-top:1.4rem">
  <h3 style="margin-top:0">Users</h3>
  <div style="overflow:auto"><table style="width:100%;border-collapse:collapse">
    <tr style="text-align:left;color:var(--dim);font-size:.74rem;text-transform:uppercase;letter-spacing:.05em">
      <th style="padding:.5rem">User</th><th>Role</th><th>Points</th><th>Solved</th><th>Assigned</th><th>Progress</th><th style="text-align:right">Actions</th></tr>
    <?php foreach ($players as $p): $sid=solved_ids($p['email']); $sc=count($sid); $pts=player_points($p['email']);
      $asg=count(assigned_ids($p['email'])); $pc=$total?round(100*$sc/$total):0; $self=$p['email']===$me['email']; ?>
      <tr style="border-top:1px solid var(--line)">
        <td style="padding:.6rem .5rem"><b><?= e($p['name']) ?></b><?= $self?' <span style="color:var(--dim)">(you)</span>':'' ?><br><span style="color:var(--dim);font-size:.8rem"><?= e($p['email']) ?></span></td>
        <td><span class="lvlpill <?= $p['role']==='admin'?'lvl-secure':'lvl-medium' ?>" style="text-transform:capitalize"><?= e($p['role']) ?></span></td>
        <td style="color:#fbbf24;font-weight:700"><?= $pts ?></td>
        <td><?= $sc ?>/<?= $total ?></td>
        <td><?= $asg ?: '<span style="color:var(--dim)">—</span>' ?></td>
        <td style="min-width:110px"><div style="height:6px;background:var(--raise);border-radius:999px;overflow:hidden"><div style="height:100%;width:<?= $pc ?>%;background:linear-gradient(90deg,#4f8cff,#7c5cff)"></div></div></td>
        <td style="text-align:right;white-space:nowrap">
          <a class="btn ghost" style="padding:.28rem .6rem;font-size:.74rem" href="/instructor.php?assign=<?= urlencode($p['email']) ?>#assign">Assign</a>
          <?php if(!$self): ?>
          <form method="post" style="display:inline"><input type="hidden" name="action" value="role"><input type="hidden" name="email" value="<?= e($p['email']) ?>"><input type="hidden" name="to" value="<?= $p['role']==='admin'?'member':'admin' ?>"><button class="btn ghost" style="padding:.28rem .6rem;font-size:.74rem"><?= $p['role']==='admin'?'Demote':'Make admin' ?></button></form>
          <form method="post" style="display:inline" onsubmit="return confirm('Reset all progress for <?= e($p['email']) ?>?')"><input type="hidden" name="action" value="reset"><input type="hidden" name="email" value="<?= e($p['email']) ?>"><button class="btn ghost" style="padding:.28rem .6rem;font-size:.74rem">Reset</button></form>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete <?= e($p['email']) ?> and all their data?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="email" value="<?= e($p['email']) ?>"><button class="btn ghost" style="padding:.28rem .6rem;font-size:.74rem;border-color:rgba(242,86,75,.4);color:#f3a09a">Delete</button></form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table></div>
</div>

<?php if ($assignFor):
  $auser = null; foreach ($players as $p) if ($p['email']===$assignFor) $auser=$p;
  if ($auser): $cur = assigned_ids($assignFor); $byapp=[]; foreach($all as $c)$byapp[$c[2]][]=$c; ?>
<div class="panel" id="assign" style="margin-top:1.4rem;border-color:var(--accent-line)">
  <h3 style="margin-top:0">Assign tests to <?= e($auser['name']) ?> <span style="color:var(--dim);font-weight:400">· <?= e($assignFor) ?></span></h3>
  <p style="color:var(--muted);margin-top:0;font-size:.88rem">Tick the challenges this member should complete. They'll see them under “Assigned to you” on their dashboard.</p>
  <form method="post"><input type="hidden" name="action" value="assign"><input type="hidden" name="player" value="<?= e($assignFor) ?>">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1rem;max-height:360px;overflow:auto;padding:.3rem">
    <?php foreach ($byapp as $app=>$cs): ?>
      <div><div style="font-weight:600;font-size:.82rem;color:var(--muted);margin-bottom:.3rem"><?= $cs[0][3] ?> <?= e($app) ?></div>
      <?php foreach ($cs as $c): ?>
        <label style="display:flex;align-items:center;gap:.5rem;text-transform:none;letter-spacing:0;margin:.25rem 0;font-size:.82rem;color:var(--fg)">
          <input type="checkbox" name="cid[]" value="<?= e($c[0]) ?>" style="width:15px" <?= in_array($c[0],$cur,true)?'checked':'' ?>> <?= e($c[1]) ?></label>
      <?php endforeach; ?></div>
    <?php endforeach; ?>
    </div>
    <div style="margin-top:1rem"><button class="btn">Save assignments</button>
      <a class="btn ghost" href="/instructor.php" style="margin-left:.5rem">Done</a></div>
  </form>
</div>
<?php endif; endif; ?>

<div class="panel" style="margin-top:1.4rem">
  <h3 style="margin-top:0">Challenge landing rate</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:.3rem 1.4rem;max-height:300px;overflow:auto">
  <?php $scount=[]; foreach (db()->query("SELECT cid,COUNT(*) n FROM solves GROUP BY cid") as $r) $scount[$r['cid']]=(int)$r['n'];
  foreach ($all as $c): $n=$scount[$c[0]]??0; ?>
    <div style="display:flex;justify-content:space-between;padding:.35rem 0;border-bottom:1px solid var(--line);font-size:.84rem">
      <span><?= $c[3] ?> <?= e($c[1]) ?></span><span style="color:var(--dim)"><?= $n ?> solves</span></div>
  <?php endforeach; ?>
  </div>
</div>
<?php foot();
