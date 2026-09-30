<?php
$a = site('address');
$o2 = site('second_office') ?: [];
?>
<section class="partner-band" aria-label="Partners">
    <svg class="partner-band__wave" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true"><path d="M0 80 C 360 0 1080 0 1440 80 L1440 80 L0 80 Z" fill="currentColor"/></svg>
    <div class="partner-band__inner container">
        <span class="partner-band__logo">BirdBarrier</span>
    </div>
    <svg class="partner-band__swoosh" viewBox="0 0 1440 40" preserveAspectRatio="none" aria-hidden="true"><path d="M0 0 C 480 40 960 40 1440 0 L1440 12 C 960 48 480 48 0 12 Z" fill="currentColor"/></svg>
</section>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <img src="<?= e(site('logo')) ?>" alt="<?= e(site('name')) ?>" width="200" height="65" loading="lazy">
            <p>Our extensive experience in eco-friendly pest control and specialized techniques ensure that we effectively remove and prevent pest issues year-round. Our comprehensive range of pest control services is designed to address all types of pest problems, regardless of size.</p>
        </div>
        <div>
            <h2 class="site-footer__h">Services</h2>
            <ul class="site-footer__links">
                <li><a href="/pest-library">Pest Library</a></li>
                <li><a href="/residential">Residential</a></li>
                <li><a href="/commercial">Commercial</a></li>
                <li><a href="/insulation-encapsulation-service">Insulation/Encapsulation</a></li>
                <li><a href="/tap-insulation">TAP Insulation</a></li>
                <li><a href="/disinfection-services">Disinfection Services</a></li>
            </ul>
        </div>
        <div>
            <h2 class="site-footer__h">Follow Us</h2>
            <ul class="site-footer__links">
                <li><a href="/service-areas">Where We Service</a></li>
                <li><a href="/resources">Articles &amp; Resources</a></li>
                <li><a href="/sds-labels">SDS &amp; Labels</a></li>
                <li><a href="/faq">FAQ</a></li>
                <li><a href="/about-us">About Us</a></li>
                <li><a href="/contact">Contact Us</a></li>
            </ul>
        </div>
        <div>
            <h2 class="site-footer__h">Contact Us</h2>
            <address class="office">
                <strong><?= e(site('legal_name')) ?></strong><br>
                <?= e($a['street']) ?><br><?= e($a['city']) ?>, <?= e($a['region']) ?> <?= e($a['postal']) ?><br>
                <a class="office__phone" href="tel:<?= e(site('phone_href')) ?>"><?= e(site('phone')) ?></a>
            </address>
            <?php if (!empty($o2['phone'])): ?>
            <address class="office">
                <strong><?= e($o2['label'] ?: site('legal_name')) ?></strong><br>
                <?php if (!empty($o2['street'])): ?><?= e($o2['street']) ?><br><?php endif; ?>
                <?= e($o2['city']) ?><br>
                <a class="office__phone" href="tel:<?= e(tel_href($o2['phone'])) ?>"><?= e($o2['phone']) ?></a>
            </address>
            <?php endif; ?>
            <?php if (!empty(site('florida_office')['phone'])): ?>
                <p class="office">South Florida: <a class="office__phone" href="tel:<?= e(site('florida_office')['phone_href']) ?>"><?= e(site('florida_office')['phone']) ?></a></p>
            <?php endif; ?>
            <p class="office"><?= e(site('hours_text')) ?><br><a href="mailto:<?= e(site('email')) ?>"><?= e(site('email')) ?></a></p>
            <?php partial('social', ['class' => 'site-footer__social']) ?>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container site-footer__bottom-inner">
            <p>&copy; <?= date('Y') ?> <?= e(site('legal_name')) ?> All Rights Reserved · <a href="/privacy-policy">Privacy Policy</a></p>
            <p class="powered">Powered By Build A Space</p>
        </div>
    </div>
</footer>
<a class="mobile-call" href="tel:<?= e(site('phone_href')) ?>">Call Now: <?= e(site('phone')) ?></a>
