<?php
$town = $page['town'];
$st = $page['state'];
$svc = $page['service_name'];
$siblings = array_filter(slugs_by_type()['service-area'] ?? [],
    fn($p) => $p['town_slug'] === $page['town_slug'] && $p['slug'] !== $page['slug']);
$cityPage = null;
foreach (slugs_by_type()['city'] ?? [] as $p) {
    if ($p['town'] === $town || $p['town_slug'] === preg_replace('/-township$/', '', $page['town_slug'])) { $cityPage = $p; break; }
}
?>
<?= render('page-hero', ['page' => $page, 'lead' => "Fast, effective " . strtolower($svc) . " for homes and businesses in $town, $st."]) ?>
<section class="section">
    <div class="container with-sidebar">
        <article class="prose">
            <h2><?= e($svc) ?> You Can Count On in <?= e($town) ?></h2>
            <p>When you need <?= e(strtolower($svc)) ?> in <?= e($town) ?>, you want a company that shows up, explains what is going on and fixes the problem the first time. Amco Pest Solutions is a family-owned, QualityPro member company with four generations of experience protecting New Jersey properties.</p>

            <h2>What to Expect</h2>
            <ol class="steps">
                <li><strong>Free inspection</strong> of your <?= e($town) ?> home or business.</li>
                <li><strong>A clear plan</strong> with pricing before any work begins.</li>
                <li><strong>Targeted treatment</strong> by a licensed technician.</li>
                <li><strong>Prevention and follow-up</strong> so the problem stays solved.</li>
            </ol>

            <p>Learn more about our <a href="<?= url($page['service_slug']) ?>"><?= e(strtolower($svc)) ?> services</a><?php if ($cityPage): ?> or see everything we offer in <a href="<?= url($cityPage['slug']) ?>"><?= e($cityPage['town']) ?></a><?php endif; ?>.</p>

            <?php if ($siblings): ?>
                <h2>Other Services in <?= e($town) ?></h2>
                <ul class="link-list">
                    <?php foreach ($siblings as $p): ?>
                        <li><a href="<?= url($p['slug']) ?>"><?= e($p['h1']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
        <aside class="sidebar">
            <?php partial('contact-form', ['page' => $page, 'title' => "$svc in $town"]) ?>
        </aside>
    </div>
</section>
