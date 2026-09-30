<?php
// Simple, original monochrome glyphs (no brand artwork) for the social links.
$labels = ['facebook' => 'f', 'instagram' => 'ig', 'linkedin' => 'in', 'youtube' => 'yt', 'google' => 'G', 'yelp' => 'y', 'x' => 'X'];
?>
<ul class="social <?= e($class ?? '') ?>">
    <?php foreach ((array) site('social') as $net => $href): ?>
        <li><a href="<?= e($href) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($net)) ?>"><?= e($labels[$net] ?? strtoupper($net[0])) ?></a></li>
    <?php endforeach; ?>
</ul>
