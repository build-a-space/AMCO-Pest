<p>Amco Pest Solutions provides pest control and termite protection across South Florida's Miami-Dade and Broward communities.</p>
<?php $fl = site('florida_office'); if (!empty($fl['phone'])): ?>
<p><strong>South Florida office:</strong> <a href="tel:<?= e($fl['phone_href']) ?>"><?= e($fl['phone']) ?></a></p>
<?php endif; ?>
<h2>South Florida Communities We Serve</h2>
<ul class="link-list link-list--columns">
    <?php foreach (towns_by_county()['fl'] ?? [] as $t): ?><li><a href="<?= url($t['slug']) ?>"><?= e($t['town']) ?></a></li><?php endforeach; ?>
</ul>
