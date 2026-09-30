<?php
$town = $page['town'];
$st = $page['state'];
$county = $page['county'] ?? null;
$neighbors = [];
if ($county) {
    foreach (towns_by_county()[$county] ?? [] as $t) {
        if ($t['slug'] !== $page['slug']) $neighbors[] = $t;
    }
}
// Town-specific service pages (e.g. /service-areas-red-bank-nj-termite-control)
$areaPages = array_filter(slugs_by_type()['service-area'] ?? [], fn($p) => town_name($p['town_slug']) === $town);
$page['faq'] = [
    ["Do you offer free pest inspections in $town?", "Yes. Amco Pest Solutions offers free inspections and estimates for homes and businesses in $town, $st."],
    ["What pests do you treat in $town?", "We handle termites, mice and rats, bed bugs, ants, cockroaches, spiders, stinging insects, mosquitoes, ticks and nuisance wildlife."],
    ["Are your treatments safe for kids and pets?", "We use an Integrated Pest Management approach with targeted, label-directed applications and will walk you through any precautions before treatment."],
];
?>
<?= render('page-hero', ['page' => $page, 'lead' => "Licensed, family-owned pest control serving $town and the surrounding area."]) ?>
<section class="section">
    <div class="container with-sidebar">
        <article class="prose">
            <h2>Your Local Exterminator in <?= e($town) ?>, <?= e($st) ?></h2>
            <p>Pests do not take a day off, and neither does our commitment to <?= e($town) ?> homeowners and businesses. Amco Pest Solutions provides inspections, treatments and prevention plans designed around local conditions, seasonal pest pressure and the way your property is built.</p>

            <h2>Pest Control Services in <?= e($town) ?></h2>
            <div class="card-grid card-grid--compact">
                <?php foreach (array_slice(SERVICES, 0, 9, true) as $slug => [$name, $blurb]): ?>
                    <a class="card" href="<?= url($slug) ?>"><h3><?= e($name) ?></h3></a>
                <?php endforeach; ?>
            </div>

            <?php if ($areaPages): ?>
                <h2>More <?= e($town) ?> Services</h2>
                <ul class="link-list">
                    <?php foreach ($areaPages as $p): ?>
                        <li><a href="<?= url($p['slug']) ?>"><?= e($p['h1']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h2>Frequently Asked Questions</h2>
            <?php foreach ($page['faq'] as [$q, $a]): ?>
                <details class="faq"><summary><?= e($q) ?></summary><p><?= e($a) ?></p></details>
            <?php endforeach; ?>

            <?php if ($neighbors): ?>
                <h2>Nearby Areas We Serve</h2>
                <ul class="link-list link-list--columns">
                    <?php foreach (array_slice($neighbors, 0, 24) as $t): ?>
                        <li><a href="<?= url($t['slug']) ?>"><?= e($t['town']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <p><a href="<?= url($county) ?>">All of <?= e(COUNTIES[$county][0]) ?> →</a></p>
            <?php endif; ?>
        </article>
        <aside class="sidebar">
            <?php partial('contact-form', ['page' => $page, 'title' => "Pest Control in $town"]) ?>
        </aside>
    </div>
</section>
<?= faq_schema($page['faq']) ?>
