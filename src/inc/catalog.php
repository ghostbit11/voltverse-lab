<?php
require_once __DIR__ . '/core.php';

/* Central registry of every challenge + player progress (the SaaS learning layer). */
function CATALOG(): array {
    // id, title, app, appicon, category, owasp, difficulty(1-4), points, flag, where(hint)
    return [
      // 🛒 Voltmart (store)
      ['sqli-search','SQL Injection in search','Voltmart','🛒','Injection','A03',3,300,'VOLT{store_sqli_admin_secret}','Store search box → UNION'],
      ['sqli-login','SQLi authentication bypass','Voltmart','🛒','Injection','A03',2,200,'VOLT{store_sqli_auth_bypass}','Store customer login'],
      ['xss-reviews','Stored XSS in reviews','Voltmart','🛒','XSS','A03',2,200,'VOLT{store_stored_xss}','Product review box'],
      ['xss-profile','Stored XSS in profile','Voltmart','🛒','XSS','A03',2,200,'VOLT{store_profile_xss}','Account → display name'],
      ['cmdi','Command injection','Voltmart','🛒','RCE','A03',4,400,'VOLT{store_command_injection}','Admin → ping tool'],
      ['lfi','Local file inclusion','Voltmart','🛒','Path traversal','A05',3,250,'VOLT{store_lfi_path_traversal}','Info pages ?page='],
      ['idor-order','IDOR in order history','Voltmart','🛒','Access control','A01',1,150,'VOLT{store_idor_other_order}','order.php?id='],
      ['broken-admin','Broken access (admin)','Voltmart','🛒','Access control','A01',1,150,'VOLT{store_broken_access_admin}','/store/admin.php'],
      ['ssrf','Server-side request forgery','Voltmart','🛒','SSRF','A10',3,350,'VOLT{store_ssrf_internal_metadata}','Admin → import image URL'],
      ['price-tamper','Price tampering','Voltmart','🛒','Business logic','A04',2,250,'VOLT{store_price_tampering}','Checkout payment'],
      ['coupon','Coupon logic flaw','Voltmart','🛒','Business logic','A04',2,200,'VOLT{store_coupon_logic}','Cart coupon'],
      ['upload','Unrestricted file upload','Voltmart','🛒','RCE','A05',3,350,'VOLT{store_unrestricted_upload}','Account → Upload avatar'],
      ['ssti','Server-side template injection','Voltmart','🛒','Injection','A03',3,350,'VOLT{store_ssti}','Gift card {{ }}'],
      ['xxe','XXE injection','Voltmart','🛒','Injection','A05',3,300,'VOLT{store_xxe}','Import wishlist XML'],
      // 🏦 Aurora Bank
      ['bank-bola','BOLA — read any account','Aurora Bank','🏦','Access control','A01',2,250,'VOLT{bank_idor_treasury}','account.php?id=5002'],
      ['bank-stmt','IDOR — any statement','Aurora Bank','🏦','Access control','A01',2,250,'VOLT{bank_statement_idor}','statements.php?acc=5002'],
      ['bank-card','Card data exposure','Aurora Bank','🏦','Sensitive data','A02',2,200,'VOLT{bank_card_data_exposure}','Cards page'],
      ['bank-neg','Negative-amount transfer','Aurora Bank','🏦','Business logic','A04',3,300,'VOLT{bank_negative_amount_logic}','Transfer'],
      ['bank-from','Transfer from any account','Aurora Bank','🏦','Access control','A01',3,300,'VOLT{bank_transfer_from_any_account}','Transfer'],
      ['bank-otp','OTP verification bypass','Aurora Bank','🏦','Auth','A07',2,250,'VOLT{bank_otp_bypass}','Wire ($50k) → any 6 digits'],
      ['bank-race','Race condition (double-spend)','Aurora Bank','🏦','Business logic','A04',3,300,'VOLT{bank_race_condition}','Rewards → Claim ×5 parallel'],
      // 🔐 VoltID (JWT SSO)
      ['jwt-none','JWT alg:none bypass','VoltID','🔐','Auth','A07',3,300,'VOLT{jwt_alg_none_bypass}','VoltID → forge alg:none'],
      ['jwt-weak','JWT weak signing secret','VoltID','🔐','Crypto','A02',3,300,'VOLT{jwt_weak_secret}','VoltID → crack HMAC secret'],
      // 🤖 Voltmart Copilot (AI)
      ['ai-sys','System prompt leakage','Voltmart Copilot','🤖','LLM','LLM07',2,250,'VOLT{ai_system_prompt_leak}','Ask to repeat system prompt'],
      ['ai-direct','Direct prompt injection','Voltmart Copilot','🤖','LLM','LLM01',2,250,'VOLT{ai_direct_prompt_injection}','Ignore instructions'],
      ['ai-tool','Tool abuse / excessive agency','Voltmart Copilot','🤖','LLM','LLM06',3,350,'VOLT{ai_tool_abuse_customer_data}','Ask for another user data'],
      ['ai-indirect','Indirect prompt injection','Voltmart Copilot','🤖','LLM','LLM01',3,350,'VOLT{ai_indirect_prompt_injection}','Summarize ticket #7'],
      ['ai-disclose','Sensitive data disclosure','Voltmart Copilot','🤖','LLM','LLM02',2,250,'VOLT{ai_sensitive_data_disclosure}','List all customers'],
      ['ai-rag','RAG data poisoning','Voltmart Copilot','🤖','LLM','LLM04',3,300,'VOLT{ai_rag_poisoning}','Add poisoned KB doc, then ask'],
      // 📦 VoltBook Microsite
      ['ms-xss','Reflected XSS','VoltBook Microsite','📦','XSS','A03',2,200,'VOLT{microsite_reflected_xss}','?q= search'],
      ['ms-redir','Open redirect','VoltBook Microsite','📦','Access control','A01',1,150,'VOLT{microsite_open_redirect}','Partner offer link'],
      // 🌐 VoltCorp Website
      ['corp-lfi','LFI / path traversal','VoltCorp Website','🌐','Path traversal','A05',3,250,'VOLT{corp_lfi_traversal}','page.php?page='],
      ['corp-csrf','CSRF (no token)','VoltCorp Website','🌐','CSRF','A05',2,200,'VOLT{corp_csrf_no_token}','subscribe.php GET'],
      // 🔌 Voltmart REST API
      ['api-bola','BOLA — read any user','Voltmart API','🔌','API access','API1',2,250,'VOLT{api_bola_user_data}','GET /users/{id}'],
      ['api-expose','Excessive data exposure','Voltmart API','🔌','API data','API3',2,200,'VOLT{api_excessive_data_exposure}','GET /users'],
      ['api-mass','Mass assignment','Voltmart API','🔌','API logic','API6',3,300,'VOLT{api_mass_assignment_admin}','POST /users is_admin:1'],
      ['api-bfla','BFLA — admin function','Voltmart API','🔌','API access','API5',3,300,'VOLT{api_bfla_admin_function}','DELETE /users/{id}'],
      ['api-auth','Broken authentication','Voltmart API','🔌','API auth','API2',2,200,'VOLT{api_broken_auth}','GET /users, no key'],
      // 🔗 Attack chains (cross-app campaigns)
      ['chain-account-takeover','Chain: Account Takeover','Campaigns','🔗','Attack chain','A01',4,500,'VOLT{chain_account_takeover}','Campaigns → complete all phases'],
      ['chain-bank-heist','Chain: The Bank Heist','Campaigns','🔗','Attack chain','A04',4,500,'VOLT{chain_bank_heist}','Campaigns → complete all phases'],
      ['chain-ai-insider','Chain: AI Insider','Campaigns','🔗','Attack chain','LLM06',4,500,'VOLT{chain_ai_insider}','Campaigns → complete all phases'],
      ['chain-server-compromise','Chain: Server Compromise','Campaigns','🔗','Attack chain','A03',4,500,'VOLT{chain_server_compromise}','Campaigns → complete all phases'],
    ];
}

function cat_by_flag(string $flag): ?array {
    $flag = trim($flag);
    foreach (CATALOG() as $c) if (strcasecmp($c[8], $flag) === 0) return $c;
    return null;
}
function cat_by_id(string $id): ?array { foreach (CATALOG() as $c) if ($c[0]===$id) return $c; return null; }
function cat_total_points(): int { return array_sum(array_map(fn($c)=>$c[7], CATALOG())); }
function cat_apps(): array { $a=[]; foreach (CATALOG() as $c){$a[$c[2]]=$a[$c[2]]??['icon'=>$c[3],'total'=>0,'pts'=>0];$a[$c[2]]['total']++;$a[$c[2]]['pts']+=$c[7];} return $a; }

/* ---- solves / progress ---- */
function ensure_solves(): void {
    db()->exec("CREATE TABLE IF NOT EXISTS solves(player TEXT, cid TEXT, ts TEXT DEFAULT (datetime('now')), PRIMARY KEY(player,cid))");
}
function solved_ids(string $email): array { ensure_solves(); $st=db()->prepare("SELECT cid FROM solves WHERE player=?"); $st->execute([$email]); return array_column($st->fetchAll(PDO::FETCH_ASSOC),'cid'); }
function record_solve(string $email, string $cid): bool {
    ensure_solves();
    $exists = db()->prepare("SELECT 1 FROM solves WHERE player=? AND cid=?"); $exists->execute([$email,$cid]);
    if ($exists->fetch()) return false;
    db()->prepare("INSERT INTO solves(player,cid) VALUES(?,?)")->execute([$email,$cid]);
    return true;
}
function is_first_blood(string $cid): bool { ensure_solves(); $st=db()->prepare("SELECT COUNT(*) FROM solves WHERE cid=?"); $st->execute([$cid]); return ((int)$st->fetchColumn())<=1; }
function player_points(string $email): int {
    $ids = solved_ids($email); $p=0; foreach (CATALOG() as $c) if (in_array($c[0],$ids,true)) $p+=$c[7];
    // subtract hint penalty (20 pts per unlocked hint) — computed directly so it's consistent everywhere
    try {
        $st = db()->prepare("SELECT COUNT(*) FROM hint_unlocks WHERE player=?"); $st->execute([$email]);
        $p -= ((int)$st->fetchColumn()) * (defined('HINT_COST') ? HINT_COST : 20);
    } catch (Throwable $e) { /* hint_unlocks table not created yet */ }
    return max(0, $p);
}
function leaderboard(int $n=20): array {
    ensure_solves();
    $rows = db()->query("SELECT p.email,p.name FROM platform_users p")->fetchAll(PDO::FETCH_ASSOC);
    $out=[]; foreach ($rows as $r){ $ids=solved_ids($r['email']); if(!$ids)continue; $out[]=['name'=>$r['name'],'email'=>$r['email'],'solved'=>count($ids),'points'=>player_points($r['email'])]; }
    usort($out, fn($a,$b)=>$b['points']<=>$a['points']);
    return array_slice($out,0,$n);
}
function player_rank(string $email): int {
    $lb = leaderboard(9999); foreach ($lb as $i=>$r) if ($r['email']===$email) return $i+1; return count($lb)+1;
}
function level_name(int $pts): string {
    return $pts>=3000?'Elite Hacker':($pts>=1500?'Hacker':($pts>=500?'Operator':'Recruit'));
}
