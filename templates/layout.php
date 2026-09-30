<?php /** @var array $page */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= meta_tags($page) ?>
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<?= schema_graph($page) ?>
</head>
<body class="tpl-<?= e($page['template']) ?>">
<a class="skip-link" href="#main">Skip to content</a>
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
