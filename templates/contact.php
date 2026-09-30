<?php $a = site('address'); ?>
<?= render('page-hero', ['page' => $page, 'lead' => 'Schedule a free inspection or ask us anything. We respond quickly.']) ?>
<section class="section">
    <div class="container with-sidebar with-sidebar--wide">
        <div>
            <?php if (isset($_GET['sent'])): ?>
                <p class="alert alert--ok" role="status">Thanks! A member of our team will contact you shortly.</p>
            <?php elseif (isset($_GET['error'])): ?>
                <p class="alert alert--err" role="alert">Something went wrong. Please check the form and try again, or call us.</p>
            <?php endif; ?>
            <?php partial('contact-form', ['page' => $page, 'title' => 'Send Us a Message']) ?>
        </div>
        <aside class="contact-info">
            <h2>Call or Visit</h2>
            <p><a class="big-phone" href="tel:<?= e(site('phone_href')) ?>"><?= e(site('phone')) ?></a></p>
            <p>Toll-free: <?= e(site('toll_free')) ?><br>South Florida office: <a href="tel:<?= e(site('florida_office')['phone_href']) ?>"><?= e(site('florida_office')['phone']) ?></a></p>
            <p>Hours: Mon–Fri 8:30am–5:30pm, Sat 8:30am–2:30pm</p>
            <p><a href="mailto:<?= e(site('email')) ?>"><?= e(site('email')) ?></a></p>
            <address><?= e(site('legal_name')) ?><br><?= e($a['street']) ?><br><?= e($a['city']) ?>, <?= e($a['region']) ?> <?= e($a['postal']) ?></address>
            <h3>Areas Served</h3>
            <p>New Jersey, the five boroughs of New York City, and South Florida. <a href="/service-areas">See all service areas</a>.</p>
        </aside>
    </div>
</section>
