<?php
/**
 * Admin authentication for /dashboard-4-admin-panel.
 *
 * Credentials live in storage/admin.json as a password hash. Create or reset them with:
 *     php scripts/admin-password.php <username>
 * On hosts without a shell, set ADMIN_USER and ADMIN_PASSWORD environment variables
 * instead; they are used only until storage/admin.json exists.
 */

const ADMIN_PATH = '/dashboard-4-admin-panel';
const ADMIN_IDLE_SECONDS = 7200;       // log out after 2 hours of inactivity
const ADMIN_MAX_ATTEMPTS = 5;          // failed logins allowed per IP...
const ADMIN_LOCK_SECONDS = 900;        // ...within this window before a 15-minute lockout

function admin_credentials(): ?array
{
    $file = ROOT . '/storage/admin.json';
    if (is_file($file)) {
        $data = json_decode((string) file_get_contents($file), true);
        if (!empty($data['username']) && !empty($data['password_hash'])) {
            return $data;
        }
    }
    $user = getenv('ADMIN_USER');
    $pass = getenv('ADMIN_PASSWORD');
    if ($user && $pass) {
        return ['username' => $user, 'password_hash' => password_hash($pass, PASSWORD_DEFAULT)];
    }
    return null;
}

function admin_set_credentials(string $username, string $password): void
{
    $dir = ROOT . '/storage';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents($dir . '/admin.json', json_encode([
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'updated' => date('c'),
    ], JSON_PRETTY_PRINT), LOCK_EX);
    @chmod($dir . '/admin.json', 0600);
}

function is_admin(): bool
{
    // Only look at the session if the browser already has one (keeps public pages cookie-free).
    if (!isset($_COOKIE['amco_sid'])) {
        return false;
    }
    start_session();
    if (empty($_SESSION['admin_user'])) {
        return false;
    }
    if (time() - ($_SESSION['admin_seen'] ?? 0) > ADMIN_IDLE_SECONDS) {
        unset($_SESSION['admin_user'], $_SESSION['admin_seen']);
        return false;
    }
    $_SESSION['admin_seen'] = time();
    return true;
}

function admin_attempts_file(): string
{
    return ROOT . '/storage/login-attempts.json';
}

function admin_is_locked(string $ip): bool
{
    $file = admin_attempts_file();
    $all = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
    $a = $all[$ip] ?? null;
    return $a && $a['count'] >= ADMIN_MAX_ATTEMPTS && time() - $a['first'] < ADMIN_LOCK_SECONDS;
}

function admin_record_attempt(string $ip, bool $success): void
{
    $file = admin_attempts_file();
    if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0775, true);
    }
    $all = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
    // Drop stale entries.
    $all = array_filter($all, fn($a) => time() - $a['first'] < ADMIN_LOCK_SECONDS);
    if ($success) {
        unset($all[$ip]);
    } else {
        $all[$ip] ??= ['count' => 0, 'first' => time()];
        $all[$ip]['count']++;
    }
    file_put_contents($file, json_encode($all), LOCK_EX);
}

/** Returns an error message, or null on success. */
function admin_login(string $username, string $password): ?string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (admin_is_locked($ip)) {
        return 'Too many failed attempts. Try again in 15 minutes.';
    }
    $creds = admin_credentials();
    if (!$creds) {
        return 'No admin account is set up yet. Run: php scripts/admin-password.php <username>';
    }
    $ok = hash_equals(strtolower($creds['username']), strtolower($username))
        && password_verify($password, $creds['password_hash']);
    admin_record_attempt($ip, $ok);
    if (!$ok) {
        usleep(400000); // slow down guessing
        return 'Incorrect username or password.';
    }
    start_session();
    session_regenerate_id(true);
    $_SESSION['admin_user'] = $creds['username'];
    $_SESSION['admin_seen'] = time();
    return null;
}

function admin_logout(): void
{
    start_session();
    $_SESSION = [];
    session_regenerate_id(true);
}
