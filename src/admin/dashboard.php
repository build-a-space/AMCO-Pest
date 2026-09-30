<?php
/**
 * Admin dashboard controller, mounted at ADMIN_PATH (/dashboard-4-admin-panel).
 * Never linked from the public site, never listed in the sitemap, always noindex.
 */

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
header('X-Frame-Options: DENY');

start_session();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_POST['action'] ?? null;
$flash = null;
$error = null;

// ---- Login / logout -------------------------------------------------------
if ($method === 'POST' && $action === 'login') {
    if (!csrf_valid()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $error = admin_login(trim((string) ($_POST['username'] ?? '')), (string) ($_POST['password'] ?? ''));
        if ($error === null) {
            header('Location: ' . ADMIN_PATH, true, 303);
            exit;
        }
    }
}

if (!is_admin()) {
    echo render('admin/login', ['error' => $error]);
    exit;
}

if ($method === 'POST' && $action === 'logout' && csrf_valid()) {
    admin_logout();
    header('Location: ' . ADMIN_PATH, true, 303);
    exit;
}

// ---- Lead download --------------------------------------------------------
if (($_GET['download'] ?? '') === 'leads') {
    $file = ROOT . '/storage/leads.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="amco-leads-' . date('Y-m-d') . '.csv"');
    echo "date,ip,name,phone,email,zip,service,message,page\n";
    if (is_file($file)) {
        readfile($file);
    }
    exit;
}

// ---- Saving ---------------------------------------------------------------
$tab = preg_replace('/[^a-z]/', '', $_GET['tab'] ?? 'business') ?: 'business';
$post = fn(string $k, int $max = 300) => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);

if ($method === 'POST' && $action && $action !== 'login') {
    if (!csrf_valid()) {
        $error = 'Your session expired. Please reload and try again.';
    } else {
        try {
            switch ($action) {
                case 'save_business':
                    $phone = $post('phone', 40);
                    $fl = $post('florida_phone', 40);
                    $hours = array_values(array_filter(array_map('trim', explode("\n", $post('hours', 500)))));
                    foreach (['email' => $post('email', 160), 'lead_email' => $post('lead_email', 160)] as $k => $v) {
                        if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
                            throw new RuntimeException("“$v” is not a valid email address.");
                        }
                    }
                    if ($phone === '') {
                        throw new RuntimeException('The main phone number cannot be empty.');
                    }
                    save_settings([
                        'name' => $post('name', 120) ?: site('name'),
                        'legal_name' => $post('legal_name', 160) ?: site('legal_name'),
                        'tagline' => $post('tagline', 200),
                        'phone' => $phone,
                        'phone_href' => tel_href($phone),
                        'toll_free' => $post('toll_free', 40),
                        'florida_office' => ['phone' => $fl, 'phone_href' => $fl !== '' ? tel_href($fl) : ''],
                        'email' => $post('email', 160),
                        'lead_email' => $post('lead_email', 160),
                        'address' => [
                            'street' => $post('street', 160), 'city' => $post('city', 80),
                            'region' => strtoupper($post('region', 2)), 'postal' => $post('postal', 10),
                            'country' => 'US',
                        ],
                        'hours_text' => $post('hours_text', 200),
                        'hours' => $hours,
                        'social' => array_filter([
                            'facebook' => $post('facebook', 300), 'instagram' => $post('instagram', 300),
                            'linkedin' => $post('linkedin', 300), 'youtube' => $post('youtube', 300),
                            'google' => $post('google', 300), 'yelp' => $post('yelp', 300),
                        ], fn($u) => $u !== '' && filter_var($u, FILTER_VALIDATE_URL)),
                    ]);
                    $flash = 'Business details saved.';
                    break;

                case 'save_branding':
                    $changes = [];
                    foreach (['logo' => 'logo_file', 'logo_light' => 'logo_light_file', 'favicon' => 'favicon_file', 'default_og_image' => 'og_file'] as $key => $field) {
                        if (!empty($_FILES[$field]['name'])) {
                            $changes[$key] = admin_store_upload($_FILES[$field], $key === 'favicon');
                        }
                        if (!empty($_POST['reset_' . $key])) {
                            $defaults = require ROOT . '/config/site.php';
                            $changes[$key] = $defaults[$key];
                        }
                    }
                    $colors = [];
                    foreach (['primary', 'accent'] as $c) {
                        $v = strtolower($post('color_' . $c, 7));
                        if (preg_match('/^#[0-9a-f]{6}$/', $v)) {
                            $colors[$c] = $v;
                        }
                    }
                    if ($colors) {
                        $changes['colors'] = $colors;
                    }
                    save_settings($changes);
                    $flash = 'Branding saved.';
                    break;

                case 'save_status':
                    save_settings([
                        'site_online' => !empty($_POST['site_online']),
                        'indexable' => !empty($_POST['indexable']),
                        'offline_message' => $post('offline_message', 500),
                    ]);
                    $flash = site('site_online') ? 'Site is ONLINE.' : 'Site is OFFLINE – visitors now see the maintenance page.';
                    break;

                case 'change_password':
                    $creds = admin_credentials();
                    if (!password_verify((string) ($_POST['current'] ?? ''), $creds['password_hash'])) {
                        throw new RuntimeException('Your current password is incorrect.');
                    }
                    $new = (string) ($_POST['new'] ?? '');
                    if (strlen($new) < 10) {
                        throw new RuntimeException('Use at least 10 characters for the new password.');
                    }
                    if ($new !== ($_POST['confirm'] ?? '')) {
                        throw new RuntimeException('The new passwords do not match.');
                    }
                    admin_set_credentials($post('username', 60) ?: $creds['username'], $new);
                    $_SESSION['admin_user'] = $post('username', 60) ?: $creds['username'];
                    $flash = 'Login details updated.';
                    break;
            }
        } catch (RuntimeException $ex) {
            $error = $ex->getMessage();
        }
    }
}

echo render('admin/dashboard', ['tab' => $tab, 'flash' => $flash, 'error' => $error]);

/**
 * Validate and store an uploaded image in public/uploads. Returns its public URL.
 * Raster images are checked with getimagesize(); SVGs are rejected if they contain
 * scripts or event handlers.
 */
function admin_store_upload(array $f, bool $allowIco = false): string
{
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . (int) $f['error'] . ').');
    }
    if ($f['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Images must be under 3 MB.');
    }
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'];
    if ($allowIco) {
        $allowed[] = 'ico';
    }
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Allowed image types: ' . implode(', ', $allowed) . '.');
    }
    $tmp = $f['tmp_name'];
    if ($ext === 'svg') {
        $svg = (string) file_get_contents($tmp);
        if (!str_contains($svg, '<svg') || preg_match('/<script|<foreignObject|\\son\\w+\\s*=|javascript:|<!ENTITY/i', $svg)) {
            throw new RuntimeException('That SVG contains unsupported or unsafe content.');
        }
    } elseif ($ext !== 'ico' && @getimagesize($tmp) === false) {
        throw new RuntimeException('That file is not a valid image.');
    }
    $dir = ROOT . '/public/uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($tmp, $dir . '/' . $name)) {
        throw new RuntimeException('Could not save the uploaded file.');
    }
    return '/uploads/' . $name;
}
