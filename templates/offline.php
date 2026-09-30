<?php $a = site('address'); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title><?= e(site('name')) ?> – Back Soon</title>
<link rel="icon" href="<?= e(site('favicon')) ?>">
<style>
  :root { --brand: <?= e(site('colors')['primary']) ?>; --accent: <?= e(site('colors')['accent']) ?>; }
  body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 16px; background: #f6f6f6;
         font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; color: #1c1c1c; text-align: center; }
  main { background: #fff; max-width: 520px; width: 100%; padding: 2.5rem 1.5rem; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,.08); border-top: 6px solid var(--brand); }
  img { max-width: 240px; width: 70%; }
  h1 { font-size: 1.5rem; margin: 1.2rem 0 .5rem; }
  a.call { display: inline-block; margin-top: 1rem; background: var(--brand); color: #fff; padding: .9rem 1.6rem; border-radius: 999px; font-weight: 800; text-decoration: none; font-size: 1.2rem; }
  p.small { color: #666; font-size: .9rem; }
</style>
</head>
<body>
<main>
  <img src="<?= e(site('logo')) ?>" alt="<?= e(site('name')) ?>">
  <h1>We'll be right back</h1>
  <p><?= e(site('offline_message')) ?></p>
  <a class="call" href="tel:<?= e(site('phone_href')) ?>"><?= e(site('phone')) ?></a>
  <p class="small"><?= e($a['street']) ?>, <?= e($a['city']) ?>, <?= e($a['region']) ?> <?= e($a['postal']) ?><br><?= e(site('hours_text')) ?></p>
</main>
</body>
</html>
