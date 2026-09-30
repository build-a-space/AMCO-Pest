#!/usr/bin/env node
/**
 * Site health check: requests every URL in data/urls.txt from a running dev
 * server and verifies status 200, a <title>, a meta description, a canonical
 * link and exactly one <h1>. Also reports internal links that 404.
 *
 *   npm run dev            # in one terminal
 *   npm run check          # in another  (BASE=http://localhost:8000 by default)
 */
import { readFile } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const BASE = process.env.BASE || 'http://localhost:8000';

const slugs = [...new Set((await readFile(path.join(ROOT, 'data/urls.txt'), 'utf8'))
  .split('\n').map((l) => l.trim()).filter((l) => l && !l.startsWith('#'))
  .map((l) => l.replace(/^\/+|\/+$/g, '')))];
const known = new Set(slugs.map((s) => '/' + s));
known.add('/');

const problems = [];
const brokenLinks = new Map();
let i = 0;

async function worker() {
  while (i < slugs.length) {
    const slug = slugs[i++];
    const url = `${BASE}/${slug}`;
    const res = await fetch(url);
    const html = await res.text();
    const issues = [];
    if (res.status !== 200) issues.push(`status ${res.status}`);
    if (!/<title>[^<]+<\/title>/.test(html)) issues.push('missing <title>');
    if (!/<meta name="description" content="[^"]+"/.test(html)) issues.push('missing meta description');
    if (!/<link rel="canonical"/.test(html)) issues.push('missing canonical');
    const h1s = (html.match(/<h1[\s>]/g) || []).length;
    if (h1s !== 1) issues.push(`${h1s} <h1> tags`);
    if (issues.length) problems.push(`/${slug}: ${issues.join(', ')}`);

    for (const [, href] of html.matchAll(/href="(\/[^"#?]*)/g)) {
      if (href.startsWith('/assets/') || known.has(href.replace(/\/$/, '') || '/')) continue;
      if (href === '/sitemap.xml' || href === '/robots.txt') continue;
      if (!brokenLinks.has(href)) brokenLinks.set(href, `/${slug}`);
    }
  }
}

await Promise.all(Array.from({ length: 12 }, worker));

console.log(`Checked ${slugs.length} URLs against ${BASE}`);
console.log(problems.length ? `\n${problems.length} page issue(s):\n` + problems.join('\n') : 'All pages OK.');
console.log(brokenLinks.size ? `\n${brokenLinks.size} internal link(s) to unknown URLs:\n` + [...brokenLinks].map(([h, from]) => `${h}  (first seen on ${from})`).join('\n') : 'No broken internal links.');
process.exit(problems.length || brokenLinks.size ? 1 : 0);
