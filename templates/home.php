<section class="hero">
    <div class="container hero__inner">
        <div class="hero__copy">
            <p class="eyebrow">Family-owned · Four generations · QualityPro certified</p>
            <h1><?= e($page['h1']) ?></h1>
            <p class="lead">Amco Pest Solutions protects homes and businesses across New Jersey, the five boroughs of New York City and South Florida from termites, rodents, bed bugs, wildlife and every pest in between.</p>
            <div class="hero__actions">
                <a class="btn btn--accent" href="/contact">Schedule a Free Inspection</a>
                <a class="btn btn--ghost" href="tel:<?= e(site('phone_href')) ?>">Call <?= e(site('phone')) ?></a>
            </div>
            <ul class="badges">
                <li>Licensed &amp; insured</li>
                <li>Authorized Sentricon® provider</li>
                <li>Termidor® certified</li>
            </ul>
        </div>
        <div class="hero__form">
            <?php partial('contact-form', ['page' => $page, 'title' => 'Get Your Free Inspection']) ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <header class="section__head">
            <h2>Pest Control Services</h2>
            <p>Residential, commercial and industrial plans built around your property, whatever the pest.</p>
        </header>
        <div class="card-grid">
            <?php foreach (array_slice(SERVICES, 0, 9, true) as $slug => [$name, $blurb]): ?>
                <a class="card" href="<?= url($slug) ?>">
                    <h3><?= e($name) ?></h3>
                    <p><?= e($blurb) ?></p>
                    <span class="card__more">Learn more →</span>
                </a>
            <?php endforeach; ?>
        </div>
        <p class="center"><a class="btn btn--outline" href="/services">View all services</a></p>
    </div>
</section>

<section class="section section--alt">
    <div class="container split">
        <div>
            <h2>Why homeowners and businesses choose Amco</h2>
            <p>Amco is a family business with four generations of experience in pest management. We have lasted because of hard work, innovation and a drive to give every client the most complete service possible.</p>
            <ul class="checklist">
                <li>Integrated Pest Management (IPM) that targets the cause, not just the symptoms</li>
                <li>Member of QualityPro, the mark of excellence in the pest control industry</li>
                <li>Authorized Sentricon® colony elimination and Termidor® provider</li>
                <li>Residential, commercial and industrial programs</li>
                <li>Wildlife exclusion, insulation and encapsulation under one roof</li>
            </ul>
            <a class="btn btn--primary" href="/about-us">About our company</a>
        </div>
        <div class="stat-grid">
            <div class="stat"><strong>4</strong><span>generations of experience</span></div>
            <div class="stat"><strong>3</strong><span>states served</span></div>
            <div class="stat"><strong>70+</strong><span>pests in our library</span></div>
            <div class="stat"><strong>Free</strong><span>inspections &amp; estimates</span></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <header class="section__head">
            <h2>Areas We Serve</h2>
            <p>Local technicians throughout New Jersey, New York City and South Florida.</p>
        </header>
        <div class="area-columns">
            <div>
                <h3>New Jersey</h3>
                <ul class="link-list">
                <?php foreach (COUNTIES as $slug => [$name, $st]): if ($st !== 'NJ') continue; ?>
                    <li><a href="<?= url($slug) ?>"><?= e($name) ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h3>New York City</h3>
                <ul class="link-list">
                <?php foreach (COUNTIES as $slug => [$name, $st]): if ($st !== 'NY') continue; ?>
                    <li><a href="<?= url($slug) ?>"><?= e($name) ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h3>South Florida</h3>
                <ul class="link-list">
                <?php foreach (towns_by_county()['fl'] ?? [] as $t): ?>
                    <li><a href="<?= url($t['slug']) ?>"><?= e($t['town']) ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section section--alt">
    <div class="container">
        <header class="section__head">
            <h2>Latest News &amp; Pest Tips</h2>
        </header>
        <div class="card-grid">
            <?php foreach (array_slice(array_reverse(slugs_by_type()['blog-post'] ?? []), 0, 3) as $post): ?>
                <a class="card" href="<?= url($post['slug']) ?>">
                    <h3><?= e($post['h1']) ?></h3>
                    <span class="card__more">Read article →</span>
                </a>
            <?php endforeach; ?>
        </div>
        <p class="center"><a class="btn btn--outline" href="/news-and-updates">All articles</a></p>
    </div>
</section>
