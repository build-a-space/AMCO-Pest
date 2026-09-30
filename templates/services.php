<?= render('page-hero', ['page' => $page, 'lead' => $page['description']]) ?>
<section class="section">
    <div class="container">
        <h2>Pest &amp; Wildlife Control</h2>
        <div class="card-grid">
            <?php foreach (SERVICES as $slug => [$name, $blurb]): ?>
                <a class="card" href="<?= url($slug) ?>"><h3><?= e($name) ?></h3><p><?= e($blurb) ?></p><span class="card__more">Learn more →</span></a>
            <?php endforeach; ?>
        </div>
        <h2 class="mt">Home Protection &amp; Specialty Services</h2>
        <div class="card-grid">
            <?php foreach (OTHER_SERVICES as $slug => [$name, $blurb]): ?>
                <a class="card" href="<?= url($slug) ?>"><h3><?= e($name) ?></h3><p><?= e($blurb) ?></p><span class="card__more">Learn more →</span></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
