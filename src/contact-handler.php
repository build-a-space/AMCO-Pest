<?php
// Handles POST /contact. Validates, drops obvious spam, stores the lead and
// optionally emails it. Responds with JSON for fetch() and redirects otherwise.


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

try {
    lead_append(['date' => date('c'), 'ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? '')] + $data);
} catch (RuntimeException $e) {
    error_log('Lead storage failed: ' . $e->getMessage());
}

if ($to = site('lead_email')) {
    $subject = 'New website lead: ' . str_replace(["\r", "\n"], ' ', $data['name']);
    $body = '';
    foreach ($data as $k => $v) {
        $body .= ucfirst($k) . ': ' . $v . "\n";
    }
    send_lead_email($to, $subject, $body, $data['email']);
}

$respond(true, 'Thanks! A member of our team will contact you shortly.');

/**
 * Email a lead. Uses the Resend API when RESEND_API_KEY is set (needed on Vercel,
 * which has no mail server); otherwise falls back to PHP mail().
 */
function send_lead_email(string $to, string $subject, string $body, string $replyTo): void
{
    $key = (string) getenv('RESEND_API_KEY');
    if ($key !== '') {
        $payload = ['from' => getenv('MAIL_FROM') ?: site('name') . ' Website <onboarding@resend.dev>',
            'to' => [$to], 'subject' => $subject, 'text' => $body];
        if (filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $payload['reply_to'] = $replyTo;
        }
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key, 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload)]);
        curl_exec($ch);
        curl_close($ch);
        return;
    }
    $headers = 'From: ' . site('email');
    if (filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers .= "\r\nReply-To: " . $replyTo;
    }
    @mail($to, $subject, $body, $headers);
}
