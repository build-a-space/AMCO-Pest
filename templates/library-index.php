<?php
$have = [];
foreach (slugs_by_type()['library'] ?? [] as $p) {
    $have[$p['pest']] = $p;
}
$grouped = [];
foreach (PEST_GROUPS as $group => $keys) {
    foreach ($keys as $k) {
        if (isset($have[$k])) {
            $grouped[$group][] = $have[$k];
            unset($have[$k]);
        }
    }
}
if ($have) {
    $grouped['More Pests'] = array_values($have);
}
?>
<?= render('page-hero', ['page' => $page, 'lead' => $page['description']]) ?>
<section class="section">
    <div class="container">
        <label class="filter"><span class="visually-hidden">Filter pests</span>
            <input type="search" placeholder="Search the pest library…" data-filter="#pest-groups">
        </label>
        <div id="pest-groups">
        <?php foreach ($grouped as $group => $items): ?>
            <section class="pest-group" data-filter-group>
                <h2><?= e($group) ?></h2>
                <ul class="chip-list">
                    <?php foreach ($items as $p): ?>
                        <li data-filter-item><a class="chip" href="<?= url($p['slug']) ?>"><?= e($p['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>
        </div>
    </div>
</section>
