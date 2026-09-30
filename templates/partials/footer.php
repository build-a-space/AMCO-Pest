<?php $a = site('address'); ?>
<?php partial('cta') ?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div>
            <img src="/assets/img/logo-light.svg" alt="<?= e(site('name')) ?>" width="180" height="48" loading="lazy">
            <p><?= e(site('tagline')) ?>. Proud member of QualityPro and an authorized Sentricon® and Termidor® provider.</p>
            <address>
                <?= e($a['street']) ?><br><?= e($a['city']) ?>, <?= e($a['region']) ?> <?= e($a['postal']) ?><br>
                <a href="tel:<?= e(site('phone_href')) ?>"><?= e(site('phone')) ?></a><br>
                Toll-free: <?= e(site('toll_free')) ?><br>
                South Florida: <a href="tel:<?= e(site('florida_office')['phone_href']) ?>"><?= e(site('florida_office')['phone']) ?></a><br>
                <a href="mailto:<?= e(site('email')) ?>"><?= e(site('email')) ?></a>
            </address>
        </div>
        <div>
            <h2 class="site-footer__h">Services</h2>
            <ul>
                <?php foreach (array_slice(SERVICES, 0, 8, true) as $slug => [$name]): ?>
                    <li><a href="<?= url($slug) ?>"><?= e($name) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <h2 class="site-footer__h">Service Areas</h2>
            <ul>
                <?php foreach (['monmouth-county', 'ocean-county', 'middlesex-county', 'bergen-county', 'essex-county', 'hudson-county'] as $c): ?>
                    <li><a href="<?= url($c) ?>"><?= e(COUNTIES[$c][0]) ?>, NJ</a></li>
                <?php endforeach; ?>
                <li><a href="/new-york">New York City</a></li>
                <li><a href="/florida">South Florida</a></li>
            </ul>
        </div>
        <div>
            <h2 class="site-footer__h">Company</h2>
            <ul>
                <li><a href="/about-us">About Us</a></li>
                <li><a href="/news-and-updates">News &amp; Updates</a></li>
                <li><a href="/pest-library">Pest Library</a></li>
                <li><a href="/faq">FAQ</a></li>
                <li><a href="/resources">Resources</a></li>
                <li><a href="/sds-labels">SDS &amp; Labels</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </div>
    </div>
    <div class="container site-footer__bottom">
        <p>&copy; <?= date('Y') ?> <?= e(site('legal_name')) ?>. All rights reserved.</p>
        <p>
            <a href="/privacy-policy">Privacy Policy</a>
            <?php foreach (site('social') as $net => $href): ?>
                · <a href="<?= e($href) ?>" rel="noopener" target="_blank"><?= e(ucfirst($net)) ?></a>
            <?php endforeach; ?>
        </p>
    </div>
</footer>
<a class="mobile-call" href="tel:<?= e(site('phone_href')) ?>">Call Now: <?= e(site('phone')) ?></a>
