<?php $formId = 'f' . substr(md5((string) mt_rand()), 0, 6); ?>
<form class="lead-form" method="post" action="/contact" data-lead-form novalidate>
    <h2 class="lead-form__title"><?= e($title ?? 'Get a Free Inspection') ?></h2>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="page" value="<?= e('/' . ($page['slug'] ?? '')) ?>">
    <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
    <div class="field"><label for="<?= $formId ?>-name">Name *</label><input id="<?= $formId ?>-name" name="name" autocomplete="name" required></div>
    <div class="field-row">
        <div class="field"><label for="<?= $formId ?>-phone">Phone</label><input id="<?= $formId ?>-phone" name="phone" type="tel" autocomplete="tel"></div>
        <div class="field"><label for="<?= $formId ?>-zip">ZIP</label><input id="<?= $formId ?>-zip" name="zip" inputmode="numeric" autocomplete="postal-code"></div>
    </div>
    <div class="field"><label for="<?= $formId ?>-email">Email</label><input id="<?= $formId ?>-email" name="email" type="email" autocomplete="email"></div>
    <div class="field"><label for="<?= $formId ?>-service">Service needed</label>
        <select id="<?= $formId ?>-service" name="service">
            <option value="">Select a service…</option>
            <?php foreach (SERVICES + OTHER_SERVICES as $slug => $s): ?>
                <option<?= (($selected ?? '') === $s[0]) ? ' selected' : '' ?>><?= e($s[0]) ?></option>
            <?php endforeach; ?>
            <option>Other</option>
        </select>
    </div>
    <div class="field"><label for="<?= $formId ?>-message">How can we help?</label><textarea id="<?= $formId ?>-message" name="message" rows="4"></textarea></div>
    <button class="btn btn--accent btn--block" type="submit">Send Request</button>
    <p class="lead-form__status" role="status" aria-live="polite"></p>
</form>
