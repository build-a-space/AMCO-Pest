<?php $byCounty = towns_by_county(); ?>
<?= render('page-hero', ['page' => $page, 'lead' => $page['description']]) ?>
<section class="section">
    <div class="container">
        <?php foreach (['NJ' => 'New Jersey', 'NY' => 'New York City'] as $st => $label): ?>
            <h2><?= e($label) ?></h2>
            <div class="county-grid">
            <?php foreach (COUNTIES as $slug => [$name, $cst]): if ($cst !== $st) continue; ?>
                <section class="county-card">
                    <h3><a href="<?= url($slug) ?>"><?= e($name) ?></a></h3>
                    <ul class="link-list link-list--compact">
                        <?php foreach ($byCounty[$slug] ?? [] as $t): ?>
                            <li><a href="<?= url($t['slug']) ?>"><?= e($t['town']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <?php foreach (['nj' => 'More New Jersey Towns', 'ny' => 'More New York Neighborhoods', 'fl' => 'South Florida'] as $k => $label): ?>
            <?php if (!empty($byCounty[$k])): ?>
                <h2><?= e($label) ?></h2>
                <ul class="link-list link-list--columns">
                    <?php foreach ($byCounty[$k] as $t): ?>
                        <li><a href="<?= url($t['slug']) ?>"><?= e($t['town']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
