<?php
/**
 * Persistent storage that works on a normal PHP host AND on Vercel.
 *
 *  - Local (default): JSON files in storage/, leads in storage/leads.csv,
 *    uploads in public/uploads/.
 *  - Vercel Blob (when BLOB_READ_WRITE_TOKEN is set): Vercel's filesystem is
 *    read-only, so data goes to Blob. Private data (settings, admin login,
 *    leads) is encrypted with APP_KEY before upload and stored under random,
 *    unguessable names; uploaded images are stored as public blobs.
 *
 * Public API:
 *   data_read(name): ?array      data_write(name, array): void
 *   lead_append(array): void     leads_all(): array (newest first)
 *   upload_image(tmpPath, ext): string (public URL)
 */

const BLOB_API = 'https://blob.vercel-storage.com';
const BLOB_CACHE_TTL = 20; // seconds a warm serverless instance trusts its cached settings

function using_blob(): bool
{
    return (string) getenv('BLOB_READ_WRITE_TOKEN') !== '';
}

// ---------------------------------------------------------------------------
// Encryption key (APP_KEY). Required on Vercel; auto-created locally.
// ---------------------------------------------------------------------------

function app_key(): string
{
    static $key = null;
    if ($key !== null) {
        return $key;
    }
    $env = (string) getenv('APP_KEY');
    if ($env !== '') {
        $raw = base64_decode(preg_replace('/^base64:/', '', $env), true);
        if ($raw === false || strlen($raw) < 32) {
            $raw = hash('sha256', $env, true);
        }
        return $key = substr($raw, 0, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }
    if (using_blob()) {
        http_response_code(500);
        exit('Server misconfigured: set the APP_KEY environment variable.');
    }
    $file = ROOT . '/storage/app.key';
    if (!is_file($file)) {
        @mkdir(dirname($file), 0775, true);
        file_put_contents($file, base64_encode(random_bytes(32)), LOCK_EX);
        @chmod($file, 0600);
    }
    return $key = base64_decode(trim((string) file_get_contents($file)));
}

function seal(string $plain): string
{
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    return $nonce . sodium_crypto_secretbox($plain, $nonce, app_key());
}

function unseal(string $sealed): ?string
{
    if (strlen($sealed) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES) {
        return null;
    }
    $plain = sodium_crypto_secretbox_open(
        substr($sealed, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
        substr($sealed, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
        app_key()
    );
    return $plain === false ? null : $plain;
}

// ---------------------------------------------------------------------------
// Vercel Blob REST helpers
// ---------------------------------------------------------------------------

function blob_request(string $method, string $url, ?string $body = null, array $headers = []): array
{
    $ch = curl_init($url);
    $h = ['authorization: Bearer ' . getenv('BLOB_READ_WRITE_TOKEN'), 'x-api-version: 11'];
    foreach ($headers as $k => $v) {
        $h[] = "$k: $v";
    }
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $h,
        CURLOPT_TIMEOUT => 15,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $res = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    if ($res === false || $code >= 400) {
        throw new RuntimeException("Blob storage error ($code): " . substr((string) $res, 0, 200));
    }
    return json_decode((string) $res, true) ?: [];
}

function blob_put(string $pathname, string $body, string $contentType, bool $randomSuffix): array
{
    return blob_request('PUT', BLOB_API . '/' . $pathname, $body, [
        'x-content-type' => $contentType,
        'x-add-random-suffix' => $randomSuffix ? '1' : '0',
        'x-allow-overwrite' => '1',
        'x-cache-control-max-age' => $randomSuffix ? '31536000' : '60',
    ]);
}

/** All blobs under a prefix, newest first. */
function blob_list(string $prefix, int $max = 1000): array
{
    $blobs = [];
    $cursor = null;
    do {
        $q = http_build_query(array_filter(['prefix' => $prefix, 'limit' => 1000, 'cursor' => $cursor]));
        $res = blob_request('GET', BLOB_API . '?' . $q);
        $blobs = array_merge($blobs, $res['blobs'] ?? []);
        $cursor = !empty($res['hasMore']) ? ($res['cursor'] ?? null) : null;
    } while ($cursor && count($blobs) < $max);
    usort($blobs, fn($a, $b) => strcmp($b['uploadedAt'] ?? '', $a['uploadedAt'] ?? ''));
    return $blobs;
}

function blob_delete(array $urls): void
{
    if ($urls) {
        blob_request('POST', BLOB_API . '/delete', json_encode(['urls' => array_values($urls)]), ['content-type' => 'application/json']);
    }
}

/** Download several blob URLs in parallel. Returns [url => body]. */
function blob_fetch_many(array $urls): array
{
    $mh = curl_multi_init();
    $handles = [];
    foreach ($urls as $u) {
        $ch = curl_init($u);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
        curl_multi_add_handle($mh, $ch);
        $handles[$u] = $ch;
    }
    do {
        $status = curl_multi_exec($mh, $running);
        if ($running) {
            curl_multi_select($mh);
        }
    } while ($running && $status === CURLM_OK);
    $out = [];
    foreach ($handles as $u => $ch) {
        $out[$u] = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE) === 200 ? (string) curl_multi_getcontent($ch) : '';
        curl_multi_remove_handle($mh, $ch);
        }
    curl_multi_close($mh);
    return $out;
}

// ---------------------------------------------------------------------------
// Key/value JSON documents (settings, admin, login-attempts)
// ---------------------------------------------------------------------------

function data_read(string $name): ?array
{
    $memo = &$GLOBALS['__data_memo'];
    $memo ??= [];
    if (array_key_exists($name, $memo)) {
        return $memo[$name];
    }
    if (!using_blob()) {
        $file = ROOT . "/storage/$name.json";
        return $memo[$name] = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: null) : null;
    }

    // Per-instance cache in /tmp so page views don't hit Blob every time.
    $cache = sys_get_temp_dir() . "/amco-$name.cache";
    if (is_file($cache) && time() - filemtime($cache) < BLOB_CACHE_TTL) {
        $plain = unseal((string) file_get_contents($cache));
        if ($plain !== null) {
            return $memo[$name] = json_decode($plain, true) ?: null;
        }
    }
    try {
        $latest = blob_list("data/$name/", 50)[0] ?? null;
        $sealed = $latest ? (blob_fetch_many([$latest['url']])[$latest['url']] ?? '') : '';
    } catch (RuntimeException $e) {
        // Blob unreachable: fall back to a stale cache rather than failing the page.
        $sealed = is_file($cache) ? (string) file_get_contents($cache) : '';
    }
    $plain = $sealed !== '' ? unseal($sealed) : null;
    @file_put_contents($cache, $sealed);
    return $memo[$name] = $plain !== null ? (json_decode($plain, true) ?: null) : null;
}

function data_write(string $name, array $data): void
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (!using_blob()) {
        $dir = ROOT . '/storage';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents("$dir/$name.json", $json, LOCK_EX);
        if ($name === 'admin') {
            @chmod("$dir/$name.json", 0600);
        }
    } else {
        $sealed = seal($json);
        $new = blob_put("data/$name/" . gmdate('Ymd\THis') . '.bin', $sealed, 'application/octet-stream', true);
        // Keep the newest few versions as a safety net; delete the rest.
        $old = array_slice(blob_list("data/$name/", 100), 5);
        blob_delete(array_column(array_filter($old, fn($b) => $b['url'] !== ($new['url'] ?? '')), 'url'));
        @file_put_contents(sys_get_temp_dir() . "/amco-$name.cache", $sealed);
    }
    $GLOBALS['__data_memo'][$name] = $data;
}

// ---------------------------------------------------------------------------
// Leads
// ---------------------------------------------------------------------------

const LEAD_FIELDS = ['date', 'ip', 'name', 'phone', 'email', 'zip', 'service', 'message', 'page'];

function lead_append(array $lead): void
{
    $row = [];
    foreach (LEAD_FIELDS as $f) {
        $row[$f] = (string) ($lead[$f] ?? '');
    }
    if (!using_blob()) {
        $dir = ROOT . '/storage';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $fh = fopen("$dir/leads.csv", 'a');
        fputcsv($fh, array_values($row), ',', '"', '');
        fclose($fh);
        return;
    }
    blob_put('leads/' . gmdate('Ymd\THis') . '.bin', seal(json_encode($row)), 'application/octet-stream', true);
}

/** @return array<int, array<string,string>> newest first */
function leads_all(int $limit = 1000): array
{
    if (!using_blob()) {
        $file = ROOT . '/storage/leads.csv';
        $rows = [];
        if (is_file($file) && ($fh = fopen($file, 'r'))) {
            while (($r = fgetcsv($fh, 0, ',', '"', '')) !== false) {
                $rows[] = array_combine(LEAD_FIELDS, array_pad(array_slice($r, 0, 9), 9, ''));
            }
            fclose($fh);
        }
        return array_slice(array_reverse($rows), 0, $limit);
    }
    $blobs = array_slice(blob_list('leads/', $limit), 0, $limit);
    $bodies = blob_fetch_many(array_column($blobs, 'url'));
    $rows = [];
    foreach ($blobs as $b) {
        $plain = unseal($bodies[$b['url']] ?? '');
        if ($plain !== null && ($r = json_decode($plain, true))) {
            $rows[] = $r;
        }
    }
    return $rows;
}

// ---------------------------------------------------------------------------
// Uploaded images
// ---------------------------------------------------------------------------

function upload_image(string $tmpPath, string $ext): string
{
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!using_blob()) {
        $dir = ROOT . '/public/uploads';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        if (!move_uploaded_file($tmpPath, "$dir/$name") && !rename($tmpPath, "$dir/$name")) {
            throw new RuntimeException('Could not save the uploaded file.');
        }
        return '/uploads/' . $name;
    }
    $types = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp',
        'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'ico' => 'image/x-icon'];
    $res = blob_put('uploads/' . $name, (string) file_get_contents($tmpPath), $types[$ext] ?? 'application/octet-stream', true);
    return $res['url'] ?? throw new RuntimeException('Upload did not return a URL.');
}
