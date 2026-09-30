<?php
$tabs = [
    'residential' => ['Residential', 'Residential Pest Control', '/residential', 'Year-round protection for your home and family.', [
        'Thorough interior and exterior inspections', 'Targeted treatments for ants, roaches, rodents, termites and more',
        'Family- and pet-conscious application methods', 'Seasonal plans that stop problems before they start', 'Free follow-up visits if pests return between services',
    ], 'tab-residential'],
    'commercial' => ['Commercial', 'Commercial Pest Control', '/commercial', 'AMCO provides comprehensive pest control services for local businesses, creating a pest-free environment for your customers and employees.', [
        'Tailored pest management solutions for businesses of all sizes', 'Minimal disruption to your business operations',
        'Licensed and certified technicians for reliable service', 'Integrated pest management strategies to maintain a pest-free environment',
        'Ongoing support and monitoring for long-term results',
    ], 'tab-commercial'],
    'managers' => ['Property Managers', 'Pest Control for Property Managers', '/apartment-complexes-condominium-associations', 'Dependable programs for apartment complexes, condominium associations and multi-unit buildings.', [
        'Scheduled service across every unit and common area', 'Fast response to tenant and resident complaints',
        'Detailed service records for boards and managers', 'Bed bug, rodent and cockroach programs for multi-family housing', 'One point of contact for every property you manage',
    ], 'tab-managers'],
];
$serviceTiles = [
    ['Insulation & Encapsulation', '/insulation-encapsulation-service', 'svc-insulation'],
    ['TAP Insulation', '/tap-insulation', 'svc-tap'],
    ['Apartment Complexes & Condominium Associations', '/apartment-complexes-condominium-associations', 'svc-apartments'],
    ['Disinfection Services', '/disinfection-services', 'svc-disinfection'],
    ['Real Estate Inspections', '/residential-services-real-estate-inspections', 'svc-real-estate'],
    ['Power Washing', '/power-washing', 'svc-power-washing'],
    ['Spotted Lantern Fly', '/spotted-lantern-fly', 'svc-lanternfly'],
    ['Christmas Light Services', '/contact', 'svc-christmas-lights'],
];
$pestCards = [
    ['Cockroach Control', 'Effective pest management to keep your home cockroach-free.', '/cockroach-control', 'pest-cockroach'],
    ['Fly Control', 'Create a fly-free environment with our expert solutions.', '/house-flies', 'pest-fly'],
    ['Yellow Jacket Control', 'Safe removal of yellow jackets to protect your family.', '/stinging-insect', 'pest-yellow-jacket'],
    ['Carpenter Bees Control', 'Expert solutions to prevent damage from carpenter bees.', '/pest-library-carpenter-bees', 'pest-carpenter-bee'],
    ['Mosquito Control', 'Protect your home from mosquito infestations.', '/mosquito-control', 'pest-mosquito'],
    ['Termite Control', 'Comprehensive termite treatment and prevention.', '/termite-control', 'pest-termite'],
    ['Ant Control', 'Get rid of ants quickly and effectively.', '/ant-control', 'pest-ant'],
    ['Rodent Control', 'Keep your property rodent-free with our expert help.', '/rodent-control', 'pest-rodent'],
];
$videos = (array) site('pest_videos');
$posts = array_slice(array_reverse(slugs_by_type()['blog-post'] ?? []), 0, 3);
$tollFree = site('toll_free') ?: site('phone');
?>
<!-- Hero -->
<section class="home-hero">
    <?= photo('hero', 'AMCO technician greeting a homeowner', 'home-hero__bg') ?>
    <div class="home-hero__overlay"></div>
    <div class="container home-hero__content">
        <h1 class="home-hero__title">
            <span class="home-hero__line home-hero__line--red">We Are The Experts</span>
            <span class="home-hero__line home-hero__line--navy">In Pest Control</span>
        </h1>
        <p>AMCO Pest Control offers expert solutions to eliminate pests in your home. Our effective treatments target common nuisances like ants, roaches, rodents and more, ensuring a safe and pest-free environment. Trust our experienced team for comprehensive inspections and tailored pest management plans that protect your home and family. Say goodbye to unwanted pests with AMCO Pest Control!</p>
        <a class="btn btn--primary" href="/contact">Contact Us Today</a>
    </div>
</section>

<!-- Residential / Commercial / Property Managers tabs -->
<section class="home-tabs" data-tabs>
    <div class="home-tabs__bar" role="tablist">
        <?php $i = 0; foreach ($tabs as $key => [$label]): ?>
            <button role="tab" id="tab-<?= $key ?>" aria-controls="panel-<?= $key ?>" aria-selected="<?= $key === 'commercial' ? 'true' : 'false' ?>" tabindex="<?= $key === 'commercial' ? '0' : '-1' ?>"><?= e($label) ?></button>
        <?php $i++; endforeach; ?>
    </div>
    <div class="container">
        <?php foreach ($tabs as $key => [$label, $heading, $link, $intro, $points, $img]): ?>
            <div class="home-tabs__panel" role="tabpanel" id="panel-<?= $key ?>" aria-labelledby="tab-<?= $key ?>"<?= $key === 'commercial' ? '' : ' hidden' ?>>
                <div class="home-tabs__text">
                    <h2><?= e($heading) ?></h2>
                    <p><?= e($intro) ?></p>
                    <ul class="checklist checklist--red">
                        <?php foreach ($points as $pt): ?><li><?= e($pt) ?></li><?php endforeach; ?>
                    </ul>
                    <p>We help protect your property and keep things running smoothly.</p>
                    <a class="btn btn--primary btn--sm" href="<?= e($link) ?>">Schedule It Now</a>
                </div>
                <?= photo($img, $heading, 'home-tabs__img') ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Stats -->
<section class="container">
    <div class="stats-bar">
        <?php foreach ((array) site('stats') as [$label, $num]): ?>
            <div class="stats-bar__item"><span><?= e($label) ?></span><strong><?= e($num) ?></strong></div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Services tiles -->
<section class="section">
    <div class="container">
        <header class="section-head">
            <h2 class="section-head__title">Learn More About Our Services</h2>
            <a class="btn btn--primary btn--sm" href="/services">View All Services</a>
        </header>
        <div class="tile-grid">
            <?php foreach ($serviceTiles as [$name, $href, $img]): ?>
                <a class="tile" href="<?= e($href) ?>">
                    <?= photo($img, $name, 'tile__img') ?>
                    <span class="tile__label"><?= e($name) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Pest library cards -->
<section class="section section--alt">
    <div class="container">
        <header class="section-head">
            <h2 class="section-head__title">Learn More About The Pests In Our Library</h2>
            <a class="btn btn--primary btn--sm" href="/pest-library">View All Pests</a>
        </header>
        <div class="pest-cards">
            <?php foreach ($pestCards as [$name, $blurb, $href, $img]): ?>
                <article class="pest-card">
                    <?= photo($img, $name, 'pest-card__img') ?>
                    <div class="pest-card__body">
                        <h3><?= e($name) ?></h3>
                        <p><?= e($blurb) ?></p>
                        <a class="btn btn--primary btn--xs" href="<?= e($href) ?>">Learn More</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Schedule a free inspection -->
<section class="schedule">
    <?= photo('schedule', 'Smiling AMCO technician', 'schedule__bg') ?>
    <div class="container schedule__inner">
        <div class="schedule__panel">
            <h2>Schedule a Free Inspection</h2>
            <p>Fill out the form below and we will get right back to you. If you have an emergency, call us at <a href="tel:<?= e(tel_href($tollFree)) ?>"><?= e($tollFree) ?></a>.</p>
            <div class="schedule__form"><?php partial('contact-form', ['page' => $page, 'bare' => true]) ?></div>
        </div>
    </div>
</section>

<!-- Why choose us + video -->
<section class="section">
    <div class="container why">
        <div>
            <div class="why__head">
                <h2>Why Choose Us?</h2>
                <a class="btn btn--primary btn--sm" href="/contact">Contact Us Today</a>
            </div>
            <div class="why__grid">
                <?php foreach ([
                    ['🔒', 'Reliable Pest Control', 'We offer dependable pest control services that ensure your home stays pest-free year-round.'],
                    ['🔄', 'Monthly Maintenance Plans', 'Protect your property from recurring pest problems with our customizable monthly plans.'],
                    ['🌿', 'Eco-Friendly Solutions', 'Our pest control methods prioritize safety and sustainability to keep your family and pets safe.'],
                    ['💰', 'Competitive Pricing', 'Quality pest control that fits your budget, providing the best value for your money.'],
                ] as [$icon, $h, $p]): ?>
                    <div class="why__item">
                        <span class="why__icon" aria-hidden="true"><?= $icon ?></span>
                        <div><h3><?= e($h) ?></h3><p><?= e($p) ?></p></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($videos): [$vName, $vUrl, $vLink] = $videos[0]; ?>
        <div class="why__video" data-pest-videos='<?= e(json_encode($videos)) ?>'>
            <h3 data-video-title>Learn About <?= e($vName) ?></h3>
            <div class="video-frame">
                <iframe loading="lazy" src="<?= e($vUrl) ?>" title="Learn about <?= e($vName) ?>" allowfullscreen allow="fullscreen"></iframe>
            </div>
            <a class="btn btn--primary btn--sm" data-video-link href="<?= e($vLink) ?>">Learn More About <?= e($vName) ?></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- SEO copy -->
<section class="container">
    <article class="seo-copy">
        <h2>Professional Pest Control In New Jersey &amp; New York</h2>
        <p>When pests like bed bugs, cockroaches, rodents or termites invade your home or business, they are more than a nuisance. They can damage property, contaminate food and put the health of your family, employees and customers at risk.</p>
        <p>AMCO Pest Solutions is a family-owned pest control company that has protected homes and businesses for generations. Our licensed technicians combine thorough inspections with targeted treatments and practical prevention, so problems are solved at the source instead of coming back next season.</p>
        <p>Every service starts with an inspection of your property. We identify the pest, find how it is getting in and what is attracting it, then build a treatment plan tailored to your home or business, whether that means a one-time service, a seasonal program or year-round protection.</p>
        <p>We serve communities across New Jersey, the five boroughs of New York City and South Florida, with residential, commercial and property-management programs. Looking for help with bed bugs, cockroaches, termites, wildlife or other pests? AMCO is ready to help.</p>
        <a class="btn btn--primary btn--sm" href="/contact">Contact Us Today</a>
    </article>
</section>

<!-- Monthly plans -->
<section class="plans">
    <?= photo('plans-bg', 'Garden and mulch background', 'plans__bg') ?>
    <div class="container plans__inner">
        <header class="section-head section-head--light">
            <h2 class="section-head__title">Learn About Our Monthly Plans</h2>
            <a class="btn btn--primary btn--sm" href="/contact">View all Plans</a>
        </header>
        <div class="plans__grid">
            <?php foreach ((array) site('plans') as $plan): ?>
                <article class="plan plan--<?= e($plan['style'] ?? 'orange') ?>">
                    <h3 class="plan__name"><?= e($plan['name']) ?></h3>
                    <p class="plan__price"><?php if (!empty($plan['old'])): ?><s><?= e($plan['old']) ?></s> <?php endif; ?><strong><?= e($plan['price']) ?></strong></p>
                    <ul class="checklist checklist--orange">
                        <?php foreach ((array) $plan['features'] as $f): ?><li><?= e($f) ?></li><?php endforeach; ?>
                    </ul>
                    <a class="btn btn--orange btn--sm" href="/contact">Contact Us</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Reviews -->
<?php if ($reviews = trim((string) site('reviews_embed'))): ?>
<section class="section">
    <div class="container">
        <h2 class="center">What our customers say</h2>
        <div class="reviews-embed"><?= $reviews ?></div>
    </div>
</section>
<?php endif; ?>

<!-- Latest news -->
<section class="section section--alt">
    <div class="container">
        <header class="section-head">
            <h2 class="section-head__title">Latest News</h2>
            <a class="btn btn--primary btn--sm" href="/news-and-updates">View All News</a>
        </header>
        <div class="news-grid">
            <?php foreach ($posts as $i => $post): ?>
                <article class="news-card">
                    <a href="<?= url($post['slug']) ?>"><?= photo('news-' . ($i + 1), $post['h1'], 'news-card__img') ?></a>
                    <h3><a href="<?= url($post['slug']) ?>"><?= e($post['h1']) ?></a></h3>
                    <a class="btn btn--primary btn--xs" href="<?= url($post['slug']) ?>">Read More</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
