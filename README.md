# VoltVerse — Cybersecurity Training Lab (BREACHR)

> ⚠️ **This application is intentionally vulnerable.** It exists to teach and practice
> web / API / LLM security. **Do not deploy it on the public internet or any network you
> don't fully control.** Run it locally only.

VoltVerse is a self-hosted, self-contained cyber range that looks and feels like a real
SaaS product. It ships **8 realistic target apps** with **42 hands-on challenges** covering
the **OWASP Web Top 10**, **API Top 10**, and **LLM Top 10** — plus a full learning layer
(flags, scoring, leaderboard, hints, walkthroughs, campaigns, an instructor console, and an
auto-detecting blue-team SOC).

Single PHP 8.2 + Apache + SQLite container. One command to run.

## Quick start

```bash
docker compose up -d --build
```

Then open **http://localhost:8100**, sign up, and start hacking.
(The **first** account you register becomes the **instructor**.)

## What's inside

| App | Focus | Example bugs |
|-----|-------|--------------|
| 🛒 **Voltmart** (store) | OWASP Web Top 10 | SQLi, stored/reflected XSS, command injection, LFI, IDOR, SSRF, SSTI, XXE, unrestricted upload, price/coupon logic |
| 🏦 **Aurora Bank** | Business logic & access control | BOLA/IDOR, card-data exposure, negative-amount transfer, transfer-from-any, OTP bypass, race condition |
| 🔐 **VoltID** (JWT SSO) | Auth / crypto | `alg:none` bypass, weak HMAC secret |
| 🤖 **Voltmart Copilot** | OWASP LLM Top 10 | prompt-leak, direct/indirect injection, tool abuse, data disclosure, RAG poisoning |
| 📦 **VoltBook Microsite** | Client-side | reflected XSS, open redirect |
| 🌐 **VoltCorp Website** | Server-side | path traversal / LFI, CSRF |
| 🔌 **Voltmart REST API** | OWASP API Top 10 | BOLA, excessive data exposure, mass assignment, BFLA, broken auth |
| 🔗 **Campaigns** | Attack chains | multi-stage, cross-app kill chains |

## Difficulty levels (bWAPP-style)

Every bug scales with a per-session difficulty (**Low → Medium → High → Secure**). At
**Secure** the code is the correct, fixed reference implementation — useful for learning the
remediation, not just the exploit.

## Learning layer

- **Flag capture & scoring** — exploit a bug, submit its `VOLT{...}` flag, earn points & first-blood.
- **Leaderboard & profile** — ranks, skill breakdown, and a printable completion certificate.
- **Hints & walkthroughs** — progressive hints (small point cost) and difficulty-aware
  step-by-step solutions. Both can be toggled per-cohort by the instructor.
- **Campaigns** — chain individual bugs across apps into realistic breach scenarios.
- **Instructor console** — cohort progress, per-challenge landing rate, content controls, account resets.
- **Blue-team SOC** — attacks are auto-detected and surfaced on a SIEM dashboard.

## Tech

PHP 8.2 · Apache · SQLite · Docker / docker-compose. No external services or API keys required.

## License / use

Provided for **education and authorized security training only.** You are responsible for how
you run it. See the warning at the top — keep it off the public internet.
