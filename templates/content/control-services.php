<p>Browse every service we offer. Not sure what you need? <a href="/contact">Ask for a free inspection</a> and we will figure it out together.</p>
<h2>Pest &amp; Wildlife Control</h2>
<ul class="link-list link-list--columns">
    <?php foreach (SERVICES as $slug => [$name]): ?><li><a href="<?= url($slug) ?>"><?= e($name) ?></a></li><?php endforeach; ?>
</ul>
<h2>Specialty Services</h2>
<ul class="link-list link-list--columns">
    <?php foreach (OTHER_SERVICES as $slug => [$name]): ?><li><a href="<?= url($slug) ?>"><?= e($name) ?></a></li><?php endforeach; ?>
</ul>
