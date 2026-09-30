<?php
/**
 * Create or reset the admin login for /dashboard-4-admin-panel.
 *
 *   php scripts/admin-password.php <username>            # prompts for the password
 *   php scripts/admin-password.php <username> <password> # non-interactive
 */
if (PHP_SAPI !== 'cli') {
    exit(1);
}
require dirname(__DIR__) . '/src/bootstrap.php';

$user = $argv[1] ?? null;
if (!$user) {
    fwrite(STDERR, "Usage: php scripts/admin-password.php <username> [password]\n");
    exit(1);
}
$pass = $argv[2] ?? null;
if ($pass === null) {
    fwrite(STDOUT, 'New password (10+ characters): ');
    system('stty -echo 2>/dev/null');
    $pass = trim((string) fgets(STDIN));
    system('stty echo 2>/dev/null');
    fwrite(STDOUT, "\n");
}
if (strlen($pass) < 10) {
    fwrite(STDERR, "Password must be at least 10 characters.\n");
    exit(1);
}
admin_set_credentials($user, $pass);
echo "Admin login saved for '$user'. Sign in at " . site('base_url') . ADMIN_PATH . "\n";
