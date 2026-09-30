# Amco Pest Solutions – website rebuild

A rebuild of amcopest.com in plain **PHP, HTML, CSS and vanilla JavaScript** (no React, no framework, no build step).
Node.js is used only for tooling: the content importer and the site checker.

Every one of the **1,741 URLs** in the live `sitemap.xml` exists here at the **same path**, so search rankings and backlinks carry over when the site is swapped.

## Run it locally

```bash
npm install          # only needed for the importer/checker tools
npm run dev          # php -S localhost:8000 -t public public/router.php
npm run check        # in a second terminal: verifies every URL, title, meta, canonical, h1 and internal link
```

Requires PHP 8.1+ and Node 20+.

## Hosting on Vercel

The site runs on Vercel with the community PHP runtime (`vercel-php`, PHP 8.4):

- `vercel.json` sends every request to `api/index.php` (which loads `public/index.php`); `public/` is served as static files. Raw `.php`/`.htaccess` paths are never served as files.
- Vercel's filesystem is read-only, so dashboard settings, the admin login, form leads and uploaded logos are stored in **Vercel Blob** (`src/storage.php`). Settings, login and leads are encrypted with `APP_KEY` before upload; images are public.
- Sign-in and form protection use signed cookies (no server sessions), so they work across Vercel's serverless instances.

Environment variables (Project → Settings → Environment Variables):

| Variable | Required | Purpose |
|---|---|---|
| `BLOB_READ_WRITE_TOKEN` | yes | Added automatically when the Blob store is connected to the project |
| `APP_KEY` | yes | Random 32-byte key (base64) used to encrypt stored data and sign cookies. Never change it after launch, or saved settings/leads become unreadable |
| `ADMIN_USER` / `ADMIN_PASSWORD` | first login | Initial dashboard login; change the password under **Account** after signing in |
| `SITE_URL` | optional | Canonical base URL (default `https://amcopest.com`) |
| `RESEND_API_KEY`, `MAIL_FROM` | optional | Email each lead via Resend (Vercel has no mail server) |

On a regular PHP host none of this is needed: without `BLOB_READ_WRITE_TOKEN` the site stores everything on local disk in `storage/` and `public/uploads/`.

## Admin dashboard

Private URL: **`/dashboard-4-admin-panel`** (not linked anywhere, not in the sitemap, always `noindex`).

Create the login once on the server:

```bash
php scripts/admin-password.php yourname      # prompts for a password (10+ characters)
```

No shell access on the host? Set `ADMIN_USER` and `ADMIN_PASSWORD` environment variables instead, sign in, then change the password under **Account** (that saves it to `storage/admin.json`).

What you can change there:

| Tab | Controls |
|---|---|
| Business details | Business/legal name, tagline, main phone, toll-free, South Florida phone, public email, lead email, address, hours (visitor text + Google format), social links |
| Logo & colors | Header logo, footer (white-text) logo, favicon, social share image, brand red/yellow |
| Site on / off | Turn the site off (visitors see a "back soon" page with your phone number, HTTP 503; you still see the full site while signed in), offline message, allow/block Google indexing |
| Leads | Every contact-form submission, newest first, with CSV download |
| Account | Change username and password |

Security: hashed passwords, CSRF tokens on every form, 15-minute lockout after 5 failed logins, 2-hour idle timeout, uploads restricted to images (unsafe SVGs rejected, no script execution in `/uploads`).
Dashboard edits are stored in `storage/settings.json` and override `config/site.php`. Keep `storage/` and `public/uploads/` writable by PHP and back them up; they're git-ignored.

## How it's organised

```
data/urls.txt            Every URL from the live sitemap – the single source of truth for routes and sitemap.xml
config/site.php          Business name, phone, address, hours, socials, lead email, indexable flag
src/catalog.php          Services, pest library groups, counties, towns→county map, wildlife species
src/pages.php            Maps each URL to a page type + title/description/h1/breadcrumbs
src/seo.php              <head> meta tags, Open Graph, canonical, schema.org JSON-LD
src/contact-handler.php  Lead form: validation, CSRF, honeypot, saves to storage/leads.csv (+ optional email)
templates/               layout, partials (header/footer/form) and one template per page type
templates/content/       Hand-written bodies for core pages (about, FAQ, etc.)
content/pages/           Imported live-site content (written by the importer; overrides templates)
public/                  Web root: index.php front controller, .htaccess, assets (css/js/img)
scripts/crawl.mjs        Importer: pulls text, meta and images from the live site
scripts/check.mjs        Health check for every page
```

### Page types (from the sitemap)

| Type | Example URL | Count |
|---|---|---|
| Wildlife/animal control × town | `/animal-control-near-you-for-red-fox-removal-newark` | 1,111 |
| Blog posts | `/news-and-updates/...` | 183 |
| Town pages | `/red-bank-nj-pest-control`, `/pest-control-exterminator-of-belmar-monmouth-county-nj` | 262 |
| Pest library | `/pest-library-termites` | 72 |
| Service × town | `/service-areas-princeton-nj-termite-control` | 55 |
| Services and core pages | `/termite-control`, `/about-us`, `/contact` | 40 |
| County hubs | `/monmouth-county` | 18 |

## Importing the real content

The build environment couldn't reach amcopest.com, so pages currently use original, SEO-structured template copy.
Once the domain is reachable (and with the site owner's permission):

```bash
npm run crawl -- --limit=5   # try a few pages first
npm run crawl                # import everything (skips pages already imported)
npm run crawl -- --force     # re-import all
```

Each page's live body, title, meta description and h1 are saved to `content/pages/` and images to
`public/assets/img/imported/`. Imported content automatically replaces the template body for that URL.
Raw HTML is kept in `content/raw/` (git-ignored) for re-parsing.

## SEO built in

- Unique title, meta description, canonical URL, Open Graph and Twitter tags on every page
- schema.org JSON-LD: `PestControlService` (local business), `WebSite`, `BreadcrumbList`, `Service`, `BlogPosting`, `FAQPage`
- Breadcrumbs and dense internal linking (town ↔ county ↔ service, species ↔ town, related pests and posts)
- `/sitemap.xml` generated from `data/urls.txt`; `/robots.txt`
- Clean URLs; trailing slashes 301 to the canonical form; HTTPS + non-www redirect in `.htaccess`
- Fast pages: one CSS file and one small deferred JS file, lazy-loaded images, caching and compression headers
- Mobile-first layout with a sticky click-to-call bar

## Before launch

1. **Verify business details** in `config/site.php` (anything marked `VERIFY`: phone, email, address, hours).
2. Run the importer, then review pages side by side with the live site.
3. Create the admin login (`php scripts/admin-password.php yourname`).
4. In the dashboard, set the lead email so form submissions are emailed (they're always listed under **Leads**).
5. In the dashboard under **Site on / off**, turn on **Allow Google to index the site**. **Until then every page is `noindex` and robots.txt blocks crawlers**, so this build can't compete with the live site.
6. Deploy `public/` as the web root on any PHP 8.1+ host (Apache `.htaccess` included; for Nginx use `try_files $uri /index.php?$query_string;`).
