<?php
$name = $page['species_name'];
$town = $page['town'];
$where = $town ? "$town, NJ" : 'New Jersey';
$groupText = [
    'bird'   => "Many birds of prey and wading birds are protected by state and federal law, so removal or relocation must be handled carefully. We assess the situation, use legal deterrents and exclusion, and coordinate with wildlife authorities when needed.",
    'bat'    => "Bats are protected and play an important role in controlling insects. Removal is done by exclusion: we identify every entry point, install one-way devices so bats can leave but not return, and seal the structure once they are gone.",
    'snake'  => "Most snakes found around New Jersey homes are harmless, but nobody wants one in the basement or garage. We safely capture and relocate snakes and reduce the cover and food sources that attract them.",
    'rodent' => "Rodents contaminate food, chew wiring and multiply quickly. We combine trapping, exclusion and monitoring to remove the current population and keep new ones out.",
    'mammal' => "Wild animals can damage roofs, attics, crawlspaces and lawns, and some carry diseases or parasites. We use humane trapping and removal, then repair and seal entry points so the animal cannot come back.",
][$page['group']] ?? '';
// Other towns for this species, and other species for this town.
$sameSpecies = []; $sameTown = [];
foreach (slugs_by_type()['animal'] ?? [] as $p) {
    if ($p['slug'] === $page['slug']) continue;
    if ($p['species_name'] === $name && $p['town']) $sameSpecies[$p['town']] = $p;
    if ($town && $p['town'] === $town) $sameTown[$p['species_name']] = $p;
}
ksort($sameSpecies); ksort($sameTown);
?>
<?= render('page-hero', ['page' => $page, 'lead' => "Humane, licensed $name removal and exclusion in $where."]) ?>
<section class="section">
    <div class="container with-sidebar">
        <article class="prose">
            <h2>Professional <?= e($name) ?> Removal in <?= e($where) ?></h2>
            <p>Finding a <?= e(strtolower($name)) ?> on your property can be stressful. Amco Pest Solutions provides wildlife inspection, humane removal, exclusion and cleanup for homes and businesses<?= $town ? ' in ' . e($town) . ' and nearby communities' : ' throughout New Jersey' ?>.</p>
            <p><?= e($groupText) ?></p>

            <h2>Our Wildlife Control Process</h2>
            <ol class="steps">
                <li><strong>Inspection</strong> to confirm the animal, how it is getting in and any damage.</li>
                <li><strong>Humane removal</strong> using methods that comply with New Jersey wildlife regulations.</li>
                <li><strong>Exclusion &amp; repairs</strong> to seal entry points and prevent re-entry.</li>
                <li><strong>Cleanup &amp; sanitation</strong> of nesting materials and droppings where needed.</li>
            </ol>
            <p>See our full <a href="/wildlife-control">wildlife control services</a> or call <a href="tel:<?= e(site('phone_href')) ?>"><?= e(site('phone')) ?></a>.</p>

            <?php if ($sameTown): ?>
                <h2>Other Wildlife We Remove in <?= e($town) ?></h2>
                <ul class="chip-list">
                    <?php foreach ($sameTown as $n => $p): ?><li><a class="chip" href="<?= url($p['slug']) ?>"><?= e($n) ?></a></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ($sameSpecies): ?>
                <h2><?= e($name) ?> Removal in Other Towns</h2>
                <ul class="link-list link-list--columns">
                    <?php foreach ($sameSpecies as $t => $p): ?><li><a href="<?= url($p['slug']) ?>"><?= e($t) ?></a></li><?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
        <aside class="sidebar">
            <?php partial('contact-form', ['page' => $page, 'selected' => 'Wildlife Control', 'title' => "$name Problem?"]) ?>
        </aside>
    </div>
</section>
