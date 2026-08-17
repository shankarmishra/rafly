<?php
/**
 * TOTP (RFC 6238) two-factor auth — admin login only.
 *
 * Self-hosted and offline by design: the secret is shared once (via QR or
 * manual entry) and every code after that is derived independently by the
 * phone and by this file from that secret plus the current time. No SMS
 * gateway, no verification API, no per-login network call, no cost.
 *
 * Pure PHP core (hash_hmac + bit-level base32), no extensions beyond what
 * ext-hash already guarantees — inc/helpers.php's password_algo() already
 * documents that this codebase cannot assume optional extensions are present
 * on the shared host, and the same caution applies here.
 */

const TOTP_PERIOD = 30;
const TOTP_DIGITS = 6;

/** RFC 4648 base32, no padding — what every authenticator app expects to scan. */
function totp_base32_encode(string $data): string
{
    if ($data === '') {
        return '';
    }

    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    $bits = '';
    for ($i = 0; $i < strlen($data); $i++) {
        $bits .= str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);
    }

    $out = '';
    foreach (str_split($bits, 5) as $chunk) {
        if (strlen($chunk) < 5) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
        }
        $out .= $alphabet[bindec($chunk)];
    }

    return $out;
}

/**
 * Decodes base32, tolerant of the padding, spaces and lowercase a human might
 * introduce copying the secret by hand rather than scanning the QR.
 */
function totp_base32_decode(string $b32): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $b32 = strtoupper((string)preg_replace('/[^A-Z2-7]/i', '', $b32));

    $bits = '';
    for ($i = 0; $i < strlen($b32); $i++) {
        $pos = strpos($alphabet, $b32[$i]);
        if ($pos === false) {
            continue;
        }
        $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
    }

    $bytes = '';
    foreach (str_split($bits, 8) as $byte) {
        // A trailing partial byte is base32 padding bits, not data — discard.
        if (strlen($byte) === 8) {
            $bytes .= chr(bindec($byte));
        }
    }

    return $bytes;
}

/** A fresh 160-bit secret (RFC 6238's recommended length), base32-encoded. */
function totp_generate_secret(): string
{
    return totp_base32_encode(random_bytes(20));
}

/** HOTP (RFC 4226): the code for one specific counter value. */
function totp_hotp(string $secretBase32, int $counter, int $digits = TOTP_DIGITS): string
{
    $key  = totp_base32_decode($secretBase32);
    $hash = hash_hmac('sha1', pack('J', $counter), $key, true);

    $offset = ord($hash[19]) & 0x0F;
    $binary = ((ord($hash[$offset]) & 0x7F) << 24)
            | (ord($hash[$offset + 1]) << 16)
            | (ord($hash[$offset + 2]) << 8)
            | ord($hash[$offset + 3]);

    $code = $binary % (10 ** $digits);
    return str_pad((string)$code, $digits, '0', STR_PAD_LEFT);
}

/** Which 30-second counter $timestamp falls in. */
function totp_counter_for(int $timestamp, int $period = TOTP_PERIOD): int
{
    return intdiv($timestamp, $period);
}

/**
 * Verify a submitted code against a ±1 step window (tolerates phone clock
 * drift of up to ~30s either side) and reject replay.
 *
 * $lastCounter is the counter last ACCEPTED for this user (stored in
 * users.totp_last_counter). A candidate counter at or before it is refused
 * even if the code matches — otherwise a code observed once (screen, camera,
 * shoulder-surf) stays valid for its whole 30s window no matter how many
 * times it's submitted.
 *
 * Returns the accepted counter on success (the caller must persist it back to
 * totp_last_counter) or false on failure. Never throws on a malformed code —
 * a login form is attacker-reachable input.
 */
function totp_verify(string $secretBase32, string $code, ?int $lastCounter, int $window = 1): int|false
{
    $code = (string)preg_replace('/\D/', '', $code);
    if (strlen($code) !== TOTP_DIGITS) {
        return false;
    }

    $now = totp_counter_for(time());

    for ($i = -$window; $i <= $window; $i++) {
        $counter = $now + $i;

        if ($lastCounter !== null && $counter <= $lastCounter) {
            continue;
        }

        if (hash_equals(totp_hotp($secretBase32, $counter), $code)) {
            return $counter;
        }
    }

    return false;
}

/**
 * otpauth:// URI for the QR code. Rendered client-side as an SVG (see
 * admin/2fa-setup.php) — the secret never needs to touch a third-party QR
 * generation API, and this URI is also shown as plain text for a phone that
 * can't scan.
 */
function totp_provisioning_uri(string $secretBase32, string $accountEmail, string $issuer = 'Rafly Admin'): string
{
    $label = rawurlencode($issuer) . ':' . rawurlencode($accountEmail);

    // RFC3986 encoding, not the default www-form-urlencoded: a space in the
    // issuer must become %20, not '+' — some authenticator apps show the '+'
    // literally instead of decoding it.
    $query = http_build_query([
        'secret'    => $secretBase32,
        'issuer'    => $issuer,
        'algorithm' => 'SHA1',
        'digits'    => TOTP_DIGITS,
        'period'    => TOTP_PERIOD,
    ], '', '&', PHP_QUERY_RFC3986);

    return 'otpauth://totp/' . $label . '?' . $query;
}

/**
 * Ten single-use recovery codes, plain text — hashing is the caller's job
 * (password_hash() with password_algo(), same as a real password) so this
 * file has no opinion on storage.
 *
 * Alphabet excludes 0/O/1/I/L: these are read aloud or typed from a printed
 * card, and those pairs are the ones people actually get wrong.
 */
function totp_generate_recovery_codes(int $count = 10): array
{
    $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $codes = [];

    for ($i = 0; $i < $count; $i++) {
        $raw = '';
        for ($j = 0; $j < 10; $j++) {
            $raw .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        $codes[] = substr($raw, 0, 5) . '-' . substr($raw, 5, 5);
    }

    return $codes;
}

/** Strips formatting so "abcd1-2345" and "ABCD1-2345" hash identically. */
function totp_normalize_recovery_code(string $code): string
{
    return strtoupper((string)preg_replace('/[^A-Z0-9]/i', '', $code));
}
