<?php
// Handles POST /contact. Validates, drops obvious spam, stores the lead and
// optionally emails it. Responds with JSON for fetch() and redirects otherwise.

start_session();

$wantsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

$respond = function (bool $ok, string $message, array $errors = []) use ($wantsJson) {
    if ($wantsJson) {
        header('Content-Type: application/json');
        http_response_code($ok ? 200 : 422);
        echo json_encode(['ok' => $ok, 'message' => $message, 'errors' => $errors]);
    } else {
        header('Location: /contact?' . ($ok ? 'sent=1' : 'error=1'), true, 303);
    }
    exit;
};

$in = fn(string $k, int $max = 200) => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);

// Honeypot field must stay empty; bots fill it in.
if ($in('website') !== '') {
    $respond(true, 'Thanks! We will be in touch shortly.');
}
if (!csrf_valid()) {
    $respond(false, 'Your session expired. Please reload the page and try again.');
}

$data = [
    'name'    => $in('name', 120),
    'phone'   => $in('phone', 40),
    'email'   => $in('email', 160),
    'zip'     => $in('zip', 10),
    'service' => $in('service', 80),
    'message' => $in('message', 3000),
    'page'    => $in('page', 300),
];

$errors = [];
if ($data['name'] === '') {
    $errors['name'] = 'Please enter your name.';
}
if ($data['phone'] === '' && $data['email'] === '') {
    $errors['phone'] = 'Please give us a phone number or email address.';
}
if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'That email address does not look right.';
}
if ($errors) {
    $respond(false, 'Please fix the highlighted fields.', $errors);
}

$dir = ROOT . '/storage';
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}
$fh = fopen($dir . '/leads.csv', 'a');
if ($fh) {
    fputcsv($fh, array_merge([date('c'), $_SERVER['REMOTE_ADDR'] ?? ''], array_values($data)));
    fclose($fh);
}

if ($to = site('lead_email')) {
    $body = '';
    foreach ($data as $k => $v) {
        $body .= ucfirst($k) . ': ' . $v . "\n";
    }
    $headers = 'From: ' . site('email');
    if ($data['email'] !== '') {
        $headers .= "\r\nReply-To: " . str_replace(["\r", "\n"], '', $data['email']);
    }
    @mail($to, 'New website lead: ' . str_replace(["\r", "\n"], ' ', $data['name']), $body, $headers);
}

$respond(true, 'Thanks! A member of our team will contact you shortly.');
