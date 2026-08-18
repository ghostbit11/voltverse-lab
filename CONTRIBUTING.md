# Contributing to VoltVerse

Thanks for your interest! VoltVerse is a security **training** project, so contributions that add
realistic vulnerabilities, new target apps, hints/walkthroughs, or platform features are all welcome.

## Ground rules

- **This project is intentionally vulnerable.** Only run it locally / on networks you control. Never
  deploy a contribution in a way that exposes a real system.
- Keep it self-contained: **PHP 8.2 + Apache + SQLite**, no external services or paid APIs at runtime.
- Every vulnerability must be **fixable** — implement the `secure` (level) branch alongside the vulnerable one.

## Project layout

```
src/
  inc/        core.php, catalog.php, layout.php, hints.php, walkthroughs.php, *_layout.php
  store/  bank/  ai/  jwt/  graphql/  oauth/  deserial/  product-site/  corp/  api/
  dashboard.php  challenges.php  campaigns.php  leaderboard.php  profile.php  soc.php  instructor.php
Dockerfile  docker-compose.yml
```

## Adding a challenge

1. Build the vulnerable behaviour in the relevant app, gated by difficulty:
   ```php
   if (lvl_secure()) { /* fixed reference implementation */ }
   else { /* vulnerable path — reveal VOLT{...} on success */ }
   ```
2. Register it in [`src/inc/catalog.php`](src/inc/catalog.php) `CATALOG()`:
   `[id, title, app, icon, category, owasp, difficulty(1-4), points, 'VOLT{flag}', where-hint]`
3. Add a **hint** (`src/inc/hints.php`) and a **walkthrough** (`src/inc/walkthroughs.php`,
   plus a `WT_SECURE()` note; add `WT_MEDHIGH()` if the payload changes across levels).
4. If it's a new app, add it to the `$base` map in `dashboard.php` and `challenges.php`.

## Running & testing

```bash
docker compose up -d --build      # http://localhost:8100
```

Exploit your challenge at **Low** and confirm the `VOLT{...}` flag appears and submits; then verify the
**Secure** level blocks it. Please describe how you tested in your PR.

## Commit / PR

- One logical change per PR, with a clear description of the vulnerability and its fix.
- Don't commit runtime artifacts (the SQLite DB, uploads) — see [`.gitignore`](.gitignore).

By contributing you agree your work is licensed under the repository's [MIT License](LICENSE).
