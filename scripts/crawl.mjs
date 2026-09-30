#!/usr/bin/env node
/**
 * Imports page content from the live amcopest.com into this site.
 * Run it only with the site owner's permission.
 *
 * For every URL in data/urls.txt it:
 *   - fetches the live page
 *   - saves title / meta description / h1 to content/pages/<slug>.json
 *   - saves the main body HTML (no header, footer, nav, scripts) to content/pages/<slug>.html
 *   - downloads images into public/assets/img/imported/ and rewrites their URLs
 *   - rewrites absolute links to amcopest.com into site-relative links
 *   - keeps the raw HTML in content/raw/ (git-ignored) so it can be re-parsed later
 *
 * Once content/pages/<slug>.html exists, the PHP site renders it in place of
 * the generated template body.
 *
 * Usage:
 *   npm run crawl                     # everything not yet imported
 *   npm run crawl -- --force          # re-import everything
 *   npm run crawl -- --only=about-us  # a single page ("/" for home)
 *   npm run crawl -- --limit=20       # first N pages (for testing)
 *   npm run crawl -- --no-images
 *
 * Behind an HTTPS proxy, run with NODE_USE_ENV_PROXY=1 (Node 22.21+ / 24+).
 */
import { readFile, writeFile, mkdir, access } from 'node:fs/promises';
import { createHash } from 'node:crypto';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import * as cheerio from 'cheerio';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const ORIGIN = process.env.SOURCE_ORIGIN || 'https://amcopest.com';
const HOSTS = new Set(['amcopest.com', 'www.amcopest.com']);
const PAGES_DIR = path.join(ROOT, 'content/pages');
const RAW_DIR = path.join(ROOT, 'content/raw');
const IMG_DIR = path.join(ROOT, 'public/assets/img/imported');
const CONCURRENCY = 4;
const DELAY_MS = 250; // be polite to the live server

const args = Object.fromEntries(process.argv.slice(2).map((a) => {
  const [k, v] = a.replace(/^--/, '').split('=');
  return [k, v ?? true];
}));

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
const exists = (p) => access(p).then(() => true, () => false);
const keyFor = (slug) => (slug === '' ? 'index' : slug.replaceAll('/', '__'));

async function loadSlugs() {
  const text = await readFile(path.join(ROOT, 'data/urls.txt'), 'utf8');
  const seen = new Set();
  for (const line of text.split('\n')) {
    const t = line.trim();
    if (!t || t.startsWith('#')) continue;
    seen.add(t.replace(/^\/+|\/+$/g, ''));
  }
  return [...seen];
}

async function fetchWithRetry(url, tries = 3) {
  for (let i = 1; ; i++) {
    try {
      const res = await fetch(url, {
        headers: { 'User-Agent': 'AmcoSiteMigration/1.0 (+site rebuild)' },
        redirect: 'follow',
      });
      return res;
    } catch (err) {
      if (i >= tries) throw err;
      await sleep(1000 * 2 ** i);
    }
  }
}

const imageCache = new Map();
async function localizeImage(src, pageUrl) {
  if (!src || src.startsWith('data:')) return src;
  let abs;
  try { abs = new URL(src, pageUrl).href; } catch { return src; }
  if (imageCache.has(abs)) return imageCache.get(abs);
  const job = (async () => {
    const ext = (path.extname(new URL(abs).pathname).toLowerCase().match(/^\.(jpe?g|png|gif|webp|svg|avif)$/) || ['.jpg'])[0];
    const name = createHash('sha1').update(abs).digest('hex').slice(0, 16) + ext;
    const file = path.join(IMG_DIR, name);
    if (!(await exists(file))) {
      const res = await fetchWithRetry(abs);
      if (!res.ok) return src;
      await writeFile(file, Buffer.from(await res.arrayBuffer()));
    }
    return `/assets/img/imported/${name}`;
  })().catch(() => src);
  imageCache.set(abs, job);
  return job;
}

function toRelative(href) {
  try {
    const u = new URL(href, ORIGIN);
    if (!HOSTS.has(u.hostname)) return href;
    const p = u.pathname.replace(/\/+$/, '') || '/';
    return p + u.search + u.hash;
  } catch {
    return href;
  }
}

async function importPage(slug) {
  const key = keyFor(slug);
  const htmlOut = path.join(PAGES_DIR, key + '.html');
  if (!args.force && (await exists(htmlOut))) return { slug, status: 'skipped' };

  const pageUrl = `${ORIGIN}/${slug}`;
  const res = await fetchWithRetry(pageUrl);
  if (!res.ok) return { slug, status: res.status };
  const raw = await res.text();
  await writeFile(path.join(RAW_DIR, key + '.html'), raw);

  const $ = cheerio.load(raw);
  const meta = {
    source: pageUrl,
    fetched: new Date().toISOString(),
    title: $('title').first().text().trim(),
    description: $('meta[name="description"]').attr('content')?.trim() || '',
    h1: $('h1').first().text().replace(/\s+/g, ' ').trim(),
    og_image: $('meta[property="og:image"]').attr('content') || '',
  };

  // Pick the main content region, dropping site chrome and third-party code.
  $('script, style, noscript, link, meta, iframe[src*="googletagmanager"]').remove();
  let $main = $('main').first();
  if (!$main.length) $main = $('#content, .content, article, [role="main"]').first();
  if (!$main.length) $main = $('body');
  $main.find('header, footer, nav, form, .header, .footer, .navbar, #header, #footer').remove();

  for (const el of $main.find('img').toArray()) {
    const $img = $(el);
    const src = $img.attr('src') || $img.attr('data-src');
    if (!args['no-images']) $img.attr('src', await localizeImage(src, pageUrl));
    $img.removeAttr('srcset').removeAttr('data-src').removeAttr('sizes');
    if (!$img.attr('loading')) $img.attr('loading', 'lazy');
    if ($img.attr('alt') === undefined) $img.attr('alt', '');
  }
  $main.find('a[href]').each((_, a) => { $(a).attr('href', toRelative($(a).attr('href'))); });
  // Inline background images in style attributes.
  for (const el of $main.find('[style*="url("]').toArray()) {
    const style = $(el).attr('style');
    const m = style.match(/url\((['"]?)([^'")]+)\1\)/);
    if (m && !args['no-images']) $(el).attr('style', style.replace(m[2], await localizeImage(m[2], pageUrl)));
  }

  await writeFile(htmlOut, ($main.html() || '').trim() + '\n');
  await writeFile(path.join(PAGES_DIR, key + '.json'), JSON.stringify(meta, null, 2) + '\n');
  return { slug, status: 200 };
}

async function main() {
  await Promise.all([PAGES_DIR, RAW_DIR, IMG_DIR].map((d) => mkdir(d, { recursive: true })));
  let slugs = await loadSlugs();
  if (args.only !== undefined) slugs = [String(args.only).replace(/^\/+|\/+$/g, '')];
  if (args.limit) slugs = slugs.slice(0, Number(args.limit));

  const results = [];
  let i = 0;
  const worker = async () => {
    while (i < slugs.length) {
      const slug = slugs[i++];
      try {
        const r = await importPage(slug);
        results.push(r);
        console.log(`${String(r.status).padEnd(7)} /${slug}`);
      } catch (err) {
        results.push({ slug, status: 'error', error: String(err) });
        console.log(`error   /${slug}  ${err}`);
      }
      await sleep(DELAY_MS);
    }
  };
  await Promise.all(Array.from({ length: CONCURRENCY }, worker));

  const summary = results.reduce((acc, r) => ((acc[r.status] = (acc[r.status] || 0) + 1), acc), {});
  await writeFile(path.join(ROOT, 'data/crawl-report.json'), JSON.stringify({ at: new Date().toISOString(), summary, results }, null, 2));
  console.log('\nSummary:', summary, '\nReport: data/crawl-report.json');
}

main().catch((err) => { console.error(err); process.exit(1); });
