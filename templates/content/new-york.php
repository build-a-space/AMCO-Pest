<?php $towns = towns_by_county(); ?>
<p>Amco Pest Solutions serves all five boroughs of New York City with residential and commercial pest control, bed bug treatment, rodent control and wildlife services.</p>
<?php foreach (COUNTIES as $slug => [$name, $st]): if ($st !== 'NY') continue; ?>
    <h2><a href="<?= url($slug) ?>"><?= e($name) ?></a></h2>
    <ul class="link-list link-list--columns">
        <?php foreach ($towns[$slug] ?? [] as $t): ?><li><a href="<?= url($t['slug']) ?>"><?= e($t['town']) ?></a></li><?php endforeach; ?>
    </ul>
<?php endforeach; ?>
