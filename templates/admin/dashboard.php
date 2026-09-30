<?php
$s = site();
$a = $s['address'];
$tabs = ['business' => 'Business details', 'homepage' => 'Homepage', 'branding' => 'Logo & colors', 'status' => 'Site on / off', 'leads' => 'Leads', 'account' => 'Account'];
$tab = isset($tabs[$tab]) ? $tab : 'business';
$csrf = csrf_token();
$field = function (string $name, string $label, ?string $value, string $type = 'text', string $help = '') {
    echo '<label>' . e($label) . ' <input type="' . $type . '" name="' . $name . '" value="' . e($value) . '"></label>';
    if ($help) echo '<p class="help">' . e($help) . '</p>';
};
?>
<?= render('admin/_head', ['title' => $tabs[$tab]]) ?>
<body class="admin">
<header class="admin-bar">
    <a href="<?= ADMIN_PATH ?>" class="admin-bar__brand"><img src="<?= e($s['logo']) ?>" alt=""> Dashboard</a>
    <span class="status-pill <?= $s['site_online'] ? 'on' : 'off' ?>"><?= $s['site_online'] ? 'Site online' : 'Site offline' ?></span>
    <a href="/" target="_blank" rel="noopener">View site ↗</a>
    <form method="post" action="<?= ADMIN_PATH ?>"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><button name="action" value="logout" class="link">Sign out</button></form>
</header>
<div class="admin-wrap">
    <nav class="admin-nav">
        <?php foreach ($tabs as $k => $label): ?>
            <a href="<?= ADMIN_PATH ?>?tab=<?= $k ?>"<?= $k === $tab ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <main class="admin-main">
        <h1><?= e($tabs[$tab]) ?></h1>
        <?php if ($flash): ?><p class="msg msg--ok" role="status"><?= e($flash) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="msg msg--err" role="alert"><?= e($error) ?></p><?php endif; ?>

        <?php if ($tab === 'business'): ?>
        <form method="post" action="<?= ADMIN_PATH ?>?tab=business" class="panel">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <fieldset><legend>Company</legend>
                <?php $field('name', 'Business name', $s['name']); ?>
                <?php $field('legal_name', 'Legal name', $s['legal_name']); ?>
                <?php $field('tagline', 'Tagline (top bar & footer)', $s['tagline']); ?>
            </fieldset>
            <fieldset><legend>Phone numbers</legend>
                <?php $field('phone', 'Main phone (Wall Township) – used across the whole site', $s['phone'], 'tel'); ?>
                <?php $field('toll_free', 'Toll-free', $s['toll_free'] ?? '', 'tel'); ?>
                <?php $field('florida_phone', 'South Florida office – shown on Florida pages', $s['florida_office']['phone'] ?? '', 'tel', 'Leave blank to hide it.'); ?>
            </fieldset>
            <fieldset><legend>Second office (footer)</legend>
                <?php $o2 = $s['second_office'] ?? []; ?>
                <?php $field('o2_label', 'Name', $o2['label'] ?? ''); ?>
                <?php $field('o2_street', 'Street', $o2['street'] ?? ''); ?>
                <?php $field('o2_city', 'City, State ZIP', $o2['city'] ?? ''); ?>
                <?php $field('o2_phone', 'Phone', $o2['phone'] ?? '', 'tel', 'Leave the phone blank to hide this office.'); ?>
            </fieldset>
            <fieldset><legend>Email</legend>
                <?php $field('email', 'Public email address', $s['email'], 'email'); ?>
                <?php $field('lead_email', 'Send website form leads to', $s['lead_email'], 'email', 'Leads are always saved in the Leads tab; add an address to also get each one by email.'); ?>
            </fieldset>
            <fieldset><legend>Address</legend>
                <?php $field('street', 'Street', $a['street']); ?>
                <div class="row">
                    <?php $field('city', 'City', $a['city']); ?>
                    <?php $field('region', 'State', $a['region']); ?>
                    <?php $field('postal', 'ZIP', $a['postal']); ?>
                </div>
            </fieldset>
            <fieldset><legend>Hours</legend>
                <?php $field('hours_text', 'Hours as shown to visitors', $s['hours_text'] ?? ''); ?>
                <label>Hours for Google (one per line, e.g. <code>Mo-Fr 08:30-17:30</code>)
                    <textarea name="hours" rows="3"><?= e(implode("\n", (array) $s['hours'])) ?></textarea></label>
            </fieldset>
            <fieldset><legend>Social profiles</legend>
                <?php foreach (['facebook', 'instagram', 'linkedin', 'youtube', 'google', 'yelp'] as $net): ?>
                    <?php $field($net, ucfirst($net) . ' URL', $s['social'][$net] ?? '', 'url'); ?>
                <?php endforeach; ?>
            </fieldset>
            <button class="btn" name="action" value="save_business">Save business details</button>
        </form>

        <?php elseif ($tab === 'homepage'): ?>
        <form method="post" action="<?= ADMIN_PATH ?>?tab=homepage" class="panel">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <fieldset><legend>Header</legend>
                <?php $field('header_line1', 'Header line 1', $s['header_line1'] ?? ''); ?>
                <?php $field('header_line2', 'Header line 2 (red, italic)', $s['header_line2'] ?? ''); ?>
                <?php $field('announcement', 'Announcement bar under the menu', $s['announcement'] ?? '', 'text', 'Leave blank to hide it.'); ?>
            </fieldset>
            <fieldset><legend>Stats bar</legend>
                <?php for ($i = 0; $i < 4; $i++): $st = $s['stats'][$i] ?? ['', '']; ?>
                    <div class="row">
                        <?php $field("stat_label_$i", 'Label', $st[0]); ?>
                        <?php $field("stat_value_$i", 'Number', $st[1]); ?>
                    </div>
                <?php endfor; ?>
            </fieldset>
            <?php for ($i = 0; $i < 3; $i++): $pl = $s['plans'][$i] ?? ['name' => '', 'old' => '', 'price' => '', 'features' => []]; ?>
                <fieldset><legend>Monthly plan <?= $i + 1 ?></legend>
                    <?php $field("plan_name_$i", 'Plan name', $pl['name']); ?>
                    <div class="row">
                        <?php $field("plan_old_$i", 'Old price (crossed out)', $pl['old'] ?? ''); ?>
                        <?php $field("plan_price_$i", 'Price', $pl['price']); ?>
                    </div>
                    <label>Features (one per line)
                        <textarea name="plan_features_<?= $i ?>" rows="5"><?= e(implode("\n", (array) $pl['features'])) ?></textarea></label>
                </fieldset>
            <?php endfor; ?>
            <fieldset><legend>Embed codes</legend>
                <label>Form embed code (shown on every page)
                    <textarea name="form_embed" rows="5" spellcheck="false"><?= e($s['form_embed'] ?? '') ?></textarea></label>
                <p class="help">Leave blank to use the website's built-in form (leads then appear under the Leads tab).</p>
                <label>Reviews embed code (homepage)
                    <textarea name="reviews_embed" rows="4" spellcheck="false"><?= e($s['reviews_embed'] ?? '') ?></textarea></label>
            </fieldset>
            <button class="btn" name="action" value="save_homepage">Save homepage</button>
        </form>

        <?php elseif ($tab === 'branding'): ?>
        <form method="post" action="<?= ADMIN_PATH ?>?tab=branding" enctype="multipart/form-data" class="panel">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <?php foreach ([
                'logo' => ['logo_file', 'Main logo (header)', 'PNG, WebP or SVG with a transparent background works best.', false],
                'logo_light' => ['logo_light_file', 'Footer logo (on dark background)', 'A version with white text.', true],
                'favicon' => ['favicon_file', 'Browser tab icon (favicon)', 'Square image, at least 64×64. SVG, PNG or ICO.', false],
                'default_og_image' => ['og_file', 'Social share image', 'Shown when a page is shared on Facebook, etc. 1200×630 JPG or PNG.', false],
            ] as $key => [$input, $label, $help, $dark]): ?>
                <fieldset class="upload">
                    <legend><?= e($label) ?></legend>
                    <div class="preview<?= $dark ? ' preview--dark' : '' ?>"><img src="<?= e($s[$key]) ?>" alt=""></div>
                    <input type="file" name="<?= $input ?>" accept="image/*,.svg,.ico">
                    <p class="help"><?= e($help) ?></p>
                    <label class="check"><input type="checkbox" name="reset_<?= $key ?>" value="1"> Restore the original</label>
                </fieldset>
            <?php endforeach; ?>
            <fieldset><legend>Brand colors</legend>
                <div class="row">
                    <label>Primary (red-orange) <input type="color" name="color_primary" value="<?= e($s['colors']['primary']) ?>"></label>
                    <label>Navy (menu bar) <input type="color" name="color_navy" value="<?= e($s['colors']['navy'] ?? '#0f227e') ?>"></label>
                    <label>Accent (yellow) <input type="color" name="color_accent" value="<?= e($s['colors']['accent']) ?>"></label>
                </div>
            </fieldset>
            <button class="btn" name="action" value="save_branding">Save branding</button>
        </form>

        <?php elseif ($tab === 'status'): ?>
        <form method="post" action="<?= ADMIN_PATH ?>?tab=status" class="panel">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <label class="switch">
                <input type="checkbox" name="site_online" value="1"<?= $s['site_online'] ? ' checked' : '' ?>>
                <span><strong>Website is online</strong><br><small>Turn off to show visitors a “back soon” page with your phone number. You can still browse the full site while signed in.</small></span>
            </label>
            <label>Message shown while offline
                <textarea name="offline_message" rows="3"><?= e($s['offline_message']) ?></textarea></label>
            <label class="switch">
                <input type="checkbox" name="indexable" value="1"<?= $s['indexable'] ? ' checked' : '' ?>>
                <span><strong>Allow Google to index the site</strong><br><small>Leave OFF until this site replaces the live one. When off, every page is marked noindex and robots.txt blocks crawlers.</small></span>
            </label>
            <button class="btn" name="action" value="save_status">Save</button>
        </form>

        <?php elseif ($tab === 'leads'):
            try { $rows = leads_all(500); } catch (RuntimeException $ex) { $rows = []; echo '<p class="msg msg--err">' . e($ex->getMessage()) . '</p>'; }
        ?>
        <p><a class="btn" href="<?= ADMIN_PATH ?>?download=leads">Download all leads (CSV)</a> <span class="help"><?= count($rows) ?> total</span></p>
        <?php if (!$rows): ?>
            <p class="panel">No form submissions yet.</p>
        <?php else: ?>
            <div class="table-wrap"><table>
                <thead><tr><th>Date</th><th>Name</th><th>Phone</th><th>Email</th><th>ZIP</th><th>Service</th><th>Message</th><th>Page</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($rows, 0, 200) as $r): ['date' => $date, 'name' => $name, 'phone' => $phone, 'email' => $email, 'zip' => $zip, 'service' => $service, 'message' => $msg, 'page' => $pg] = $r + array_fill_keys(LEAD_FIELDS, ''); ?>
                    <tr>
                        <td><?= e(date('M j, Y g:ia', strtotime($date) ?: 0)) ?></td>
                        <td><?= e($name) ?></td>
                        <td><?php if ($phone): ?><a href="tel:<?= e(tel_href($phone)) ?>"><?= e($phone) ?></a><?php endif; ?></td>
                        <td><?php if ($email): ?><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a><?php endif; ?></td>
                        <td><?= e($zip) ?></td>
                        <td><?= e($service) ?></td>
                        <td class="wrap"><?= e($msg) ?></td>
                        <td><?= e($pg) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>

        <?php elseif ($tab === 'account'): ?>
        <form method="post" action="<?= ADMIN_PATH ?>?tab=account" class="panel">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <?php $field('username', 'Username', admin_user() ?? ''); ?>
            <label>Current password <input type="password" name="current" autocomplete="current-password" required></label>
            <label>New password (10+ characters) <input type="password" name="new" autocomplete="new-password" minlength="10" required></label>
            <label>Confirm new password <input type="password" name="confirm" autocomplete="new-password" minlength="10" required></label>
            <button class="btn" name="action" value="change_password">Update login</button>
        </form>
        <p class="help">Bookmark this page: <code><?= e(site('base_url') . ADMIN_PATH) ?></code>. It is not linked anywhere on the public site.</p>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
