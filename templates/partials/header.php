<?php
$nav = [
    ['Services', '/services', [
        ['Termite Control', '/termite-control'], ['Rodent Control', '/rodent-control'],
        ['Bed Bug Control', '/bed-bug-control'], ['Wildlife Control', '/wildlife-control'],
        ['Mosquito Control', '/mosquito-control'], ['Tick Control', '/tick-control'],
        ['Ant Control', '/ant-control'], ['Cockroach Control', '/cockroach-control'],
        ['Spider Control', '/spider-control'], ['Stinging Insects', '/stinging-insect'],
        ['Bird Control', '/bird-control'], ['Insulation & Encapsulation', '/insulation-encapsulation-service'],
        ['All Services', '/services'],
    ]],
    ['Residential', '/residential', null],
    ['Commercial', '/commercial', null],
    ['Pest Library', '/pest-library', null],
    ['Service Areas', '/service-areas', [
        ['New Jersey', '/service-areas'], ['New York City', '/new-york'], ['South Florida', '/florida'],
    ]],
    ['About', '/about-us', [
        ['About Us', '/about-us'], ['News & Updates', '/news-and-updates'], ['FAQ', '/faq'], ['Resources', '/resources'],
    ]],
];
$current = '/' . ($page['slug'] ?? '');
?>
<div class="topbar">
    <div class="container topbar__inner">
        <span><?= e(site('tagline')) ?></span>
        <a href="tel:<?= e(site('phone_href')) ?>" class="topbar__phone">Call <?= e(site('phone')) ?></a>
    </div>
</div>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="logo" href="/" aria-label="<?= e(site('name')) ?> home">
            <img src="<?= e(site('logo')) ?>" alt="<?= e(site('name')) ?>" width="170" height="55">
        </a>
        <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav">
            <span class="nav-toggle__bar"></span><span class="visually-hidden">Menu</span>
        </button>
        <nav id="site-nav" class="site-nav" aria-label="Main">
            <ul>
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
            <a class="btn btn--accent site-nav__cta" href="/contact">Free Inspection</a>
        </nav>
    </div>
</header>
