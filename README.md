<div align="center">

<img src="assets/banner.svg" alt="VoltVerse — cybersecurity training range" width="100%"/>

<br/>

[![License: MIT](https://img.shields.io/badge/License-MIT-22d3ee.svg?style=flat-square)](LICENSE)
![PHP](https://img.shields.io/badge/PHP-8.2-777bb4?style=flat-square&logo=php&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-ready-2496ed?style=flat-square&logo=docker&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-embedded-003b57?style=flat-square&logo=sqlite&logoColor=white)
![Challenges](https://img.shields.io/badge/challenges-48-818cf8?style=flat-square)
![OWASP](https://img.shields.io/badge/OWASP-Web%20%7C%20API%20%7C%20LLM%20Top%2010-34d399?style=flat-square)
![Status](https://img.shields.io/badge/status-active-success?style=flat-square)

**A self-hosted cyber range that looks like a real SaaS product — 11 realistic target apps, 48 hands-on challenges, and a full learning platform.**

[Quick start](#-quick-start) · [What's inside](#-whats-inside) · [Challenge list](CHALLENGES.md) · [Learning layer](#-learning-layer) · [Architecture](#-architecture) · [Contributing](CONTRIBUTING.md)

</div>

---

> [!WARNING]
> **This application is intentionally vulnerable.** It exists to teach and practice web / API / LLM
> security. **Do not deploy it on the public internet or any network you don't fully control.** Run it locally.

VoltVerse (codename **BREACHR**) packs the **OWASP Web Top 10**, **API Top 10**, and **LLM Top 10** into
one polished, self-contained platform — with flags, scoring, a leaderboard, progressive hints,
difficulty-aware walkthroughs, cross-app attack campaigns, an instructor console, and an
auto-detecting blue-team SOC. One PHP + Apache + SQLite container. One command to run.

## 🚀 Quick start

```bash
git clone https://github.com/ghostbit11/voltverse-lab.git
cd voltverse-lab
docker compose up -d --build
```

Open **http://localhost:8100**, sign up, and start hacking.
The **first** account you register automatically becomes the **instructor**.

## 🎯 What's inside

| App | Focus | Example vulnerabilities |
|-----|-------|-------------------------|
| 🛒 **Voltmart** (store) | OWASP Web Top 10 | SQLi · stored/reflected XSS · command injection · LFI · IDOR · SSRF · SSTI · XXE · unrestricted upload · price/coupon logic |
| 🏦 **Aurora Bank** | Business logic & access control | BOLA/IDOR · card-data exposure · negative-amount transfer · transfer-from-any · OTP bypass · race condition |
| 🔐 **VoltID** (JWT SSO) | Auth / crypto | `alg:none` bypass · weak HMAC secret |
| 🤖 **Voltmart Copilot** | OWASP LLM Top 10 | system-prompt leak · direct/indirect injection · tool abuse · data disclosure · RAG poisoning |
| 📦 **VoltBook Microsite** | Client-side | reflected XSS · open redirect |
| 🌐 **VoltCorp Website** | Server-side | path traversal / LFI · CSRF |
| 🔌 **Voltmart REST API** | OWASP API Top 10 | BOLA · excessive data exposure · mass assignment · BFLA · broken auth |
| 📡 **VoltData** (GraphQL) | GraphQL security | introspection exposure · BOLA · resolver SQL injection |
| 🔑 **VoltConnect** (OAuth SSO) | OAuth 2.0 | `redirect_uri` open redirect · missing-`state` login CSRF |
| 🧩 **VoltSync** | Deserialization | PHP object injection |
| 🔗 **Campaigns** | Attack chains | multi-stage, cross-app kill chains |

See **[CHALLENGES.md](CHALLENGES.md)** for the full, auto-generated list of all 48 challenges.

## 🧠 Learning layer

- **🚩 Flag capture & scoring** — exploit a bug, submit its `VOLT{...}` flag, earn points & first-blood.
- **🏆 Leaderboard & profile** — ranks, skill breakdown, and a printable completion certificate.
- **💡 Hints & 📖 walkthroughs** — progressive hints (small point cost) and **difficulty-aware**
  step-by-step solutions. Both toggle per-cohort from the instructor console.
- **⛓️ Campaigns** — chain individual bugs across apps into realistic breach scenarios.
- **🎓 Instructor console** — cohort progress, per-challenge landing rate, content controls, account resets.
- **🛡️ Blue-team SOC** — attacks are auto-detected and surfaced on a live SIEM dashboard.

## 🏗 Architecture

```mermaid
flowchart LR
    U([Trainee]) -->|login| P[VoltVerse Platform]
    P --> D[Dashboard / Challenges / Campaigns]
    D --> T{{11 vulnerable target apps}}
    T -->|exploit → VOLT flag| F[Flag engine]
    F --> S[(SQLite<br/>scores · solves · hints)]
    F --> L[Leaderboard / Profile]
    T -.request signatures.-> SOC[🛡️ Blue-team SOC / SIEM]
    I[🎓 Instructor console] --> S
    classDef c fill:#0d1426,stroke:#22d3ee,color:#e2e8f0;
    class P,D,T,F,S,L,SOC,I c;
```

Every app reads a per-session **difficulty** cookie and switches its own code path — from textbook-vulnerable
to a hardened reference implementation — so the same bug can be practised at four levels.

## 🎚 Difficulty levels

Inspired by bWAPP — every vulnerability scales:

| Level | Behaviour |
|-------|-----------|
| 🟢 **Low** | Textbook vulnerability, no defences. |
| 🟠 **Medium** | Naive filters (blacklists) that can be bypassed. |
| 🔴 **High** | Stronger but incomplete protection with a gap to find. |
| 🔵 **Secure** | Correct, fixed implementation — the remediation reference (not exploitable). |

## 🧩 Tech stack

**PHP 8.2** · **Apache** · **SQLite** · **Docker / docker-compose**. No external services, no API keys, no internet required at runtime.

## 👥 Roles (multi-tenant)

The first account you register becomes the **super administrator** (lab owner). From there:

| Role | Can do |
|------|--------|
| 👑 **Super admin** | Turn any lab on/off, manage every user, reset any password, reset shared lab data. Configures (doesn't compete); can open any lab to test it. |
| 🛡 **Admin** (e.g. a team manager) | Set an **organisation name**, create their own members, assign specific tests, toggle hints/walkthroughs, view scores, export CSV. Sees **only their own users**, isolated from other orgs. |
| 👤 **Member** | Solves the tests assigned to them; earns points and a certificate stamped with their org's name. |

Turn off **open self-registration** (Admin console) so only admins provision accounts. Admin-created users are forced to set their own password on first sign-in.

## 🛠 Operations & deployment

- **Data persistence** — user accounts, scores and settings live in a named Docker volume (`voltverse_data`), so they **survive `docker compose up --build`**. ⚠️ `docker compose down -v` **deletes the volume and all user data** — use plain `docker compose down` to stop.
- **Backups** — snapshot the database anytime:
  ```bash
  ./backup.sh            # or:  ./backup.ps1   (Windows)   → backups/voltverse_<timestamp>.db
  ./restore.sh backups/voltverse_<timestamp>.db   # or ./restore.ps1 -File ...
  ```
  Schedule `backup.sh` (cron / Task Scheduler) before running a cohort.
- **Reset shared lab state** — the target apps use shared demo accounts, so trainee-injected data (stored-XSS reviews, changed profile names, extra orders, altered balances) is visible to everyone. The super admin's dashboard has a **“Reset lab data”** button that restores the demo data without touching any scores. Run it between cohorts. *(Per-user app sandboxing is a future enhancement — for large concurrent classes, have trainees work an app in turn or reset between sessions.)*
- **Deployment** — this app is **intentionally vulnerable**. Never expose it to the public internet. Run it on `localhost` or an internal/VPN-only network; if teammates need access, front it with a reverse proxy that requires its own authentication.

## 📜 License

[MIT](LICENSE) — for **education and authorized security training only**. Keep it off the public internet (see the warning above).

<div align="center"><sub>Built for hands-on security learning · ⭐ star the repo if it helped you learn.</sub></div>
