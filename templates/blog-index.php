<?php
$posts = array_reverse(slugs_by_type()['blog-post'] ?? []); // newest last in the sitemap
$perPage = 24;
$total = max(1, (int) ceil(count($posts) / $perPage));
$n = max(1, min($total, (int) ($_GET['page'] ?? 1)));
$shown = array_slice($posts, ($n - 1) * $perPage, $perPage);
?>
<?= render('page-hero', ['page' => $page, 'lead' => $page['description']]) ?>
<section class="section">
    <div class="container">
        <label class="filter"><span class="visually-hidden">Search articles</span>
            <input type="search" placeholder="Search articles on this page…" data-filter="#post-grid">
        </label>
        <div class="card-grid" id="post-grid">
            <?php foreach ($shown as $post): ?>
                <a class="card" href="<?= url($post['slug']) ?>" data-filter-item>
                    <h2 class="card__title"><?= e($post['h1']) ?></h2>
                    <span class="card__more">Read article →</span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if ($total > 1): ?>
            <nav class="pager" aria-label="Pagination">
                <?php for ($i = 1; $i <= $total; $i++): ?>
                    <a href="/news-and-updates<?= $i > 1 ? '?page=' . $i : '' ?>"<?= $i === $n ? ' aria-current="page"' : '' ?>><?= $i ?></a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>
