<?php
/**
 * Admin authentication for /dashboard-4-admin-panel.
 *
 * Credentials are stored as a password hash (storage/admin.json locally,
 * encrypted in Vercel Blob in production). Sign-in state is a signed,
 * HttpOnly cookie that expires after ADMIN_IDLE_SECONDS of inactivity. Create or reset them with:
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
    $data = data_read('admin');
    if (!empty($data['username']) && !empty($data['password_hash'])) {
        return $data;
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
    data_write('admin', [
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'updated' => date('c'),
    ]);
}

/** Changes whenever the password changes, so old sign-in cookies stop working. */
function admin_fingerprint(array $creds): string
{
    return substr(hash_hmac('sha256', $creds['username'] . '|' . $creds['password_hash'], app_key()), 0, 16);
}

function admin_issue_cookie(array $creds): void
{
    set_app_cookie('amco_admin', sign_token([
        'u' => $creds['username'], 'f' => admin_fingerprint($creds), 'exp' => time() + ADMIN_IDLE_SECONDS,
    ]));
}

/** Username of the signed-in admin, or null. */
function admin_user(): ?string
{
    static $user = false;
    if ($user !== false) {
        return $user;
    }
    $tok = verify_token((string) ($_COOKIE['amco_admin'] ?? ''));
    $creds = $tok ? admin_credentials() : null;
    if (!$tok || !$creds || ($tok['exp'] ?? 0) < time() || !hash_equals(admin_fingerprint($creds), (string) ($tok['f'] ?? ''))) {
        return $user = null;
    }
    // Sliding expiry: refresh when less than half the idle window remains.
    if ($tok['exp'] - time() < ADMIN_IDLE_SECONDS / 2 && !headers_sent()) {
        admin_issue_cookie($creds);
    }
    return $user = $creds['username'];
}

function is_admin(): bool
{
    return isset($_COOKIE['amco_admin']) && admin_user() !== null;
}

function admin_is_locked(string $ip): bool
{
    $all = data_read('login-attempts') ?? [];
    $a = $all[$ip] ?? null;
    return $a && $a['count'] >= ADMIN_MAX_ATTEMPTS && time() - $a['first'] < ADMIN_LOCK_SECONDS;
}

function admin_record_attempt(string $ip, bool $success): void
{
    $all = data_read('login-attempts') ?? [];
    if ($success && !isset($all[$ip])) {
        return; // nothing to clear – avoid a needless write
    }
    // Drop stale entries.
    $all = array_filter($all, fn($a) => time() - $a['first'] < ADMIN_LOCK_SECONDS);
    if ($success) {
        unset($all[$ip]);
    } else {
        $all[$ip] ??= ['count' => 0, 'first' => time()];
        $all[$ip]['count']++;
    }
    data_write('login-attempts', $all);
}

/** Returns an error message, or null on success. */
function admin_login(string $username, string $password): ?string
{
    // On Vercel the client IP arrives in x-real-ip; elsewhere use REMOTE_ADDR.
    $ip = $_SERVER['HTTP_X_REAL_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
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
    admin_issue_cookie($creds);
    return null;
}

function admin_logout(): void
{
    set_app_cookie('amco_admin', '', time() - 3600);
}
