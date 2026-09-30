<?php
$posts = slugs_by_type()['blog-post'] ?? [];
$words = array_filter(explode('-', $page['post_slug']), fn($w) => strlen($w) > 4);
$related = [];
foreach ($posts as $p) {
    if ($p['slug'] === $page['slug']) continue;
    $score = count(array_intersect($words, explode('-', $p['post_slug'])));
    if ($score) $related[] = [$score, $p];
}
usort($related, fn($a, $b) => $b[0] <=> $a[0]);
?>
<article class="section">
    <div class="container with-sidebar">
        <div class="prose">
            <header class="post-head">
                <p class="eyebrow">News &amp; Updates</p>
                <h1><?= e($page['h1']) ?></h1>
            </header>
            <p class="notice">This article's text has not been imported yet. Run <code>npm run crawl</code> to pull it from the live site.</p>
            <?php if ($related): ?>
                <h2>Related Articles</h2>
                <ul class="link-list">
                    <?php foreach (array_slice($related, 0, 5) as [, $p]): ?>
                        <li><a href="<?= url($p['slug']) ?>"><?= e($p['h1']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <aside class="sidebar">
            <?php partial('contact-form', ['page' => $page]) ?>
        </aside>
    </div>
</article>
