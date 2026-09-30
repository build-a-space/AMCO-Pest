<?php $towns = towns_by_county()[$page['county']] ?? []; ?>
<?= render('page-hero', ['page' => $page, 'lead' => $page['description']]) ?>
<section class="section">
    <div class="container with-sidebar">
        <article class="prose">
            <h2>Local Pest Control Across <?= e($page['name']) ?></h2>
            <p>Amco Pest Solutions provides residential and commercial pest control throughout <?= e($page['name']) ?>. From termite protection and rodent exclusion to bed bug treatments and humane wildlife removal, our licensed technicians know the pests that are common in this area and how to stop them.</p>

            <?php if ($towns): ?>
                <h2>Towns We Serve in <?= e($page['name']) ?></h2>
                <ul class="link-list link-list--columns">
                    <?php foreach ($towns as $t): ?>
                        <li><a href="<?= url($t['slug']) ?>"><?= e($t['town']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h2>Services Available in <?= e($page['name']) ?></h2>
            <ul class="link-list link-list--columns">
                <?php foreach (SERVICES as $slug => [$name]): ?>
                    <li><a href="<?= url($slug) ?>"><?= e($name) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </article>
        <aside class="sidebar">
            <?php partial('contact-form', ['page' => $page]) ?>
        </aside>
    </div>
</section>
