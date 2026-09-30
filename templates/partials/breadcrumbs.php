<nav class="breadcrumbs container" aria-label="Breadcrumb">
    <ol>
    <?php foreach ($crumbs as [$label, $href]): ?>
        <li><?php if ($href): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?></li>
    <?php endforeach; ?>
    </ol>
</nav>
