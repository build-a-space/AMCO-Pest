<?php
$name = $page['name'];
$others = array_slice(array_diff_key(SERVICES, [$page['service'] => 1]), 0, 6, true);
?>
<?= render('page-hero', ['page' => $page, 'lead' => $page['blurb']]) ?>
<section class="section">
    <div class="container with-sidebar">
        <article class="prose">
            <h2>Professional <?= e($name) ?> from a Family-Owned Company</h2>
            <p>Every property is different, so every <?= e(strtolower($name)) ?> plan starts with a thorough inspection. A licensed Amco technician identifies what you are dealing with, finds out how it is getting in and what is attracting it, and then recommends a treatment plan that fits your home or business.</p>

            <h2>Our <?= e($name) ?> Process</h2>
            <ol class="steps">
                <li><strong>Inspection.</strong> We inspect the interior and exterior to confirm the problem, its extent and the conditions that support it.</li>
                <li><strong>Custom plan.</strong> You get a clear explanation of the recommended treatment, timeline and cost, with no pressure.</li>
                <li><strong>Treatment.</strong> We use targeted, label-directed products and methods chosen for effectiveness and safety around families and pets.</li>
                <li><strong>Prevention.</strong> We seal entry points where appropriate and share practical steps to keep the problem from coming back.</li>
                <li><strong>Follow-up.</strong> Ongoing monitoring and service plans keep your property protected year-round.</li>
            </ol>

            <h2>Why Choose Amco Pest Solutions?</h2>
            <ul class="checklist">
                <li>Four generations of pest management experience</li>
                <li>QualityPro member company with licensed, trained technicians</li>
                <li>Integrated Pest Management approach</li>
                <li>Residential and commercial service across NJ, NYC and South Florida</li>
            </ul>

            <?php if ($page['library'] && slug_exists($page['library'])): ?>
                <p>Want to learn more first? Read our guide in the <a href="<?= url($page['library']) ?>">pest library</a>.</p>
            <?php endif; ?>
        </article>
        <aside class="sidebar">
            <?php partial('contact-form', ['page' => $page, 'selected' => $name, 'title' => "Request $name"]) ?>
        </aside>
    </div>
</section>
<section class="section section--alt">
    <div class="container">
        <h2>Other Services</h2>
        <div class="card-grid">
            <?php foreach ($others as $slug => [$n, $b]): ?>
                <a class="card" href="<?= url($slug) ?>"><h3><?= e($n) ?></h3><p><?= e($b) ?></p></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
