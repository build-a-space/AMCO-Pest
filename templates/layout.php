<?php /** @var array $page */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= meta_tags($page) ?>
<link rel="icon" href="<?= e(site('favicon')) ?>">
<link rel="apple-touch-icon" href="/assets/img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<style>:root{--brand:<?= e(site('colors')['primary']) ?>;--accent:<?= e(site('colors')['accent']) ?>}</style>
<?= schema_graph($page) ?>
</head>
<body class="tpl-<?= e($page['template']) ?>">
<a class="skip-link" href="#main">Skip to content</a>
<?php if (!site('site_online')): ?><div class="admin-preview">Site is OFFLINE for visitors – you are seeing it because you are signed in. <a href="<?= ADMIN_PATH ?>?tab=status">Change</a></div><?php endif; ?>
<?php partial('header', ['page' => $page]) ?>
<main id="main">
<?php if (!empty($page['breadcrumbs'])) partial('breadcrumbs', ['crumbs' => $page['breadcrumbs']]); ?>
<?php if (!empty($page['imported_html'])): ?>
    <?= render('imported', ['page' => $page]) ?>
<?php else: ?>
    <?= render($page['template'], ['page' => $page]) ?>
<?php endif; ?>
</main>
<?php partial('footer', ['page' => $page]) ?>
<script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
