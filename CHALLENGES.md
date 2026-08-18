# Challenge catalog

**48** hands-on challenges across **11** apps. Flags use the `VOLT{...}` format.
Difficulty: 1 = Easy · 2 = Medium · 3 = Hard · 4 = Expert. Guidance (hints + difficulty-aware walkthroughs) lives in-app on the Challenges page.

## 🛒 Voltmart

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| SQL Injection in search | Injection | A03 | 3 | 300 |
| SQLi authentication bypass | Injection | A03 | 2 | 200 |
| Stored XSS in reviews | XSS | A03 | 2 | 200 |
| Stored XSS in profile | XSS | A03 | 2 | 200 |
| Command injection | RCE | A03 | 4 | 400 |
| Local file inclusion | Path traversal | A05 | 3 | 250 |
| IDOR in order history | Access control | A01 | 1 | 150 |
| Broken access (admin) | Access control | A01 | 1 | 150 |
| Server-side request forgery | SSRF | A10 | 3 | 350 |
| Price tampering | Business logic | A04 | 2 | 250 |
| Coupon logic flaw | Business logic | A04 | 2 | 200 |
| Unrestricted file upload | RCE | A05 | 3 | 350 |
| Server-side template injection | Injection | A03 | 3 | 350 |
| XXE injection | Injection | A05 | 3 | 300 |

## 🏦 Aurora Bank

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| BOLA — read any account | Access control | A01 | 2 | 250 |
| IDOR — any statement | Access control | A01 | 2 | 250 |
| Card data exposure | Sensitive data | A02 | 2 | 200 |
| Negative-amount transfer | Business logic | A04 | 3 | 300 |
| Transfer from any account | Access control | A01 | 3 | 300 |
| OTP verification bypass | Auth | A07 | 2 | 250 |
| Race condition (double-spend) | Business logic | A04 | 3 | 300 |

## 🔐 VoltID

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| JWT alg:none bypass | Auth | A07 | 3 | 300 |
| JWT weak signing secret | Crypto | A02 | 3 | 300 |

## 🤖 Voltmart Copilot

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| System prompt leakage | LLM | LLM07 | 2 | 250 |
| Direct prompt injection | LLM | LLM01 | 2 | 250 |
| Tool abuse / excessive agency | LLM | LLM06 | 3 | 350 |
| Indirect prompt injection | LLM | LLM01 | 3 | 350 |
| Sensitive data disclosure | LLM | LLM02 | 2 | 250 |
| RAG data poisoning | LLM | LLM04 | 3 | 300 |

## 📦 VoltBook Microsite

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| Reflected XSS | XSS | A03 | 2 | 200 |
| Open redirect | Access control | A01 | 1 | 150 |

## 🌐 VoltCorp Website

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| LFI / path traversal | Path traversal | A05 | 3 | 250 |
| CSRF (no token) | CSRF | A05 | 2 | 200 |

## 🔌 Voltmart API

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| BOLA — read any user | API access | API1 | 2 | 250 |
| Excessive data exposure | API data | API3 | 2 | 200 |
| Mass assignment | API logic | API6 | 3 | 300 |
| BFLA — admin function | API access | API5 | 3 | 300 |
| Broken authentication | API auth | API2 | 2 | 200 |

## 📡 VoltData

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| GraphQL introspection exposure | API data | API3 | 2 | 200 |
| GraphQL BOLA (object level) | API access | API1 | 2 | 250 |
| GraphQL resolver SQL injection | Injection | A03 | 3 | 350 |

## 🔑 VoltConnect

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| OAuth redirect_uri open redirect | Access control | A01 | 3 | 300 |
| OAuth missing state (login CSRF) | CSRF | A01 | 2 | 250 |

## 🧩 VoltSync

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| PHP object injection | Deserialization | A08 | 4 | 400 |

## 🔗 Campaigns

| Challenge | Category | OWASP | Difficulty | Points |
|---|---|---|:--:|--:|
| Chain: Account Takeover | Attack chain | A01 | 4 | 500 |
| Chain: The Bank Heist | Attack chain | A04 | 4 | 500 |
| Chain: AI Insider | Attack chain | LLM06 | 4 | 500 |
| Chain: Server Compromise | Attack chain | A03 | 4 | 500 |

---

**Total points available: 13650** · see the app pages via the dashboard to attempt each one.
