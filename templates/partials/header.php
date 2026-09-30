<?php
$pestServices = [];
foreach (SERVICES as $slug => [$name]) {
    $pestServices[] = [$name, url($slug)];
}
$areas = [];
foreach (COUNTIES as $slug => [$name, $st]) {
    if ($st === 'NJ') $areas[] = [$name, url($slug)];
}
$areas[] = ['New York City', '/new-york'];
$areas[] = ['South Florida', '/florida'];
$areas[] = ['All Service Areas', '/service-areas'];

$nav = [
    ['Home', '/', null],
    ['Services', '/services', [
        ['Residential', '/residential'], ['Commercial', '/commercial'],
        ['Insulation & Encapsulation', '/insulation-encapsulation-service'], ['TAP Insulation', '/tap-insulation'],
        ['Apartments & Condo Associations', '/apartment-complexes-condominium-associations'],
        ['Disinfection Services', '/disinfection-services'], ['Real Estate Inspections', '/residential-services-real-estate-inspections'],
        ['Power Washing', '/power-washing'], ['All Services', '/services'],
    ]],
    ['Pest Control', '/control-services', $pestServices],
    ['Where We Service', '/service-areas', $areas],
    ['Christmas Lights by AMCO', '/contact', null],
    ['Pest Library', '/pest-library', null],
    ['News and Updates', '/news-and-updates', null],
    ['About Us', '/about-us', [
        ['About Us', '/about-us'], ['FAQ', '/faq'], ['Resources', '/resources'], ['SDS & Labels', '/sds-labels'],
    ]],
    ['Contact', '/contact', null],
];
$current = '/' . ($page['slug'] ?? '');
$tollFree = site('toll_free') ?: site('phone');
?>
<div class="topbar">
    <div class="container topbar__inner">
        <nav class="topbar__links" aria-label="Quick links">
            <a href="/">Home</a>
            <a href="/news-and-updates">Blog</a>
            <a href="/faq">FAQ</a>
        </nav>
        <a href="tel:<?= e(tel_href($tollFree)) ?>" class="topbar__phone">Give Us A Call: <?= e($tollFree) ?></a>
        <?php partial('social', ['class' => 'topbar__social']) ?>
    </div>
</div>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="logo" href="/" aria-label="<?= e(site('name')) ?> home">
            <img src="<?= e(site('logo')) ?>" alt="<?= e(site('name')) ?>" width="170" height="55">
        </a>
        <div class="site-header__tagline">
            <span class="site-header__line1"><?= e(site('header_line1')) ?></span>
            <span class="site-header__line2"><?= e(site('header_line2')) ?></span>
        </div>
        <a class="btn btn--primary site-header__cta" href="/contact">Book A Service Now</a>
        <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav">
            <span class="nav-toggle__bar"></span><span class="visually-hidden">Menu</span>
        </button>
    </div>
    <nav id="site-nav" class="site-nav" aria-label="Main">
        <ul class="container">
        <?php foreach ($nav as [$label, $href, $children]): ?>
            <li class="<?= $children ? 'has-sub' : '' ?>">
                <a href="<?= e($href) ?>"<?= $current === $href ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
                <?php if ($children): ?>
                    <button class="sub-toggle" aria-label="Show <?= e($label) ?> menu" aria-expanded="false"></button>
                    <ul class="sub">
                    <?php foreach ($children as [$cl, $ch]): ?>
                        <li><a href="<?= e($ch) ?>"><?= e($cl) ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </nav>
    <?php if (site('announcement')): ?><div class="announce"><?= e(site('announcement')) ?></div><?php endif; ?>
</header>
