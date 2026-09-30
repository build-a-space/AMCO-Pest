<?php
$name = $page['name'];
$lc = strtolower($name);
// Which service page best matches this pest?
$service = null;
foreach (SERVICES as $slug => [, , $lib]) {
    if ($lib === $page['slug']) { $service = $slug; break; }
}
if (!$service) {
    foreach (PEST_GROUPS as $group => $keys) {
        if (in_array($page['pest'], $keys, true)) {
            $service = [
                'Stinging Insects' => 'stinging-insect', 'Ants & Termites' => 'ant-control', 'Rodents' => 'rodent-control',
                'Wildlife' => 'wildlife-control', 'Spiders' => 'spider-control', 'Biting & Blood-Feeding Pests' => 'flea-and-tick',
            ][$group] ?? 'services';
            break;
        }
    }
}
$service ??= 'services';
$siblings = [];
foreach (PEST_GROUPS as $keys) {
    if (in_array($page['pest'], $keys, true)) {
        foreach ($keys as $k) {
            if ($k !== $page['pest'] && slug_exists('pest-library-' . $k)) $siblings[] = $k;
        }
    }
}
?>
<?= render('page-hero', ['page' => $page, 'lead' => "Identification, habits and control of $lc."]) ?>
<section class="section">
    <div class="container with-sidebar">
        <article class="prose">
            <h2>How to Identify <?= e($name) ?></h2>
            <p>Correct identification is the first step in effective control. If you are not sure what you are seeing, take a clear photo and share it with our team – we will help you identify it and explain your options.</p>

            <h2>Why <?= e($name) ?> Become a Problem</h2>
            <p>Pests move onto a property looking for food, water and shelter. Understanding what attracts <?= e($lc) ?> helps you remove those conditions and makes professional treatment last longer.</p>

            <h2>Preventing <?= e($name) ?></h2>
            <ul class="checklist">
                <li>Seal cracks, gaps and openings around doors, windows, utilities and the foundation.</li>
                <li>Reduce moisture by fixing leaks and improving drainage and ventilation.</li>
                <li>Store food in sealed containers and keep garbage in lidded bins.</li>
                <li>Trim vegetation away from the house and remove clutter where pests hide.</li>
            </ul>

            <h2>Professional <?= e($name) ?> Control</h2>
            <p>Amco Pest Solutions uses an Integrated Pest Management approach: inspection, targeted treatment and prevention. <a href="<?= url($service) ?>">See our related service</a> or <a href="/contact">schedule a free inspection</a>.</p>

            <?php if ($siblings): ?>
                <h2>Related Pests</h2>
                <ul class="chip-list">
                    <?php foreach ($siblings as $k): ?>
                        <li><a class="chip" href="<?= url('pest-library-' . $k) ?>"><?= e(PEST_NAMES[$k] ?? humanize($k)) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
        <aside class="sidebar">
            <?php partial('contact-form', ['page' => $page, 'title' => "Dealing with $lc?"]) ?>
        </aside>
    </div>
</section>
