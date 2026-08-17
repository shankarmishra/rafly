<?php
/**
 * Contact-form handler.
 *
 * Responsibilities are limited to validating and storing the submission.
 * The success PAGE lives in thank-you.php — keeping both here previously forced
 * a 140-line nowdoc that duplicated the site's CSS, nav and modal, and meant a
 * browser refresh re-posted the form and appended a duplicate lead row.
 */

require __DIR__ . '/inc/bootstrap.php';

// Debug flag and error reporting now come from inc/config.php (APP_DEBUG),
// which defaults to OFF everywhere except recognised local hosts.
send_security_headers('form');

/**
 * @param string|null $csrfToken Fresh token to hand back after rotation, so the
 *                               page's forms stay usable without a reload.
 *
 * Every JSON response also carries a freshly seeded anti-spam challenge.
 * antibot_check() and form_timing_ok() are single-use by design, so by the time
 * this function runs the challenge on the visitor's page is already spent —
 * whether they succeeded or failed. Without re-issuing it, the next attempt from
 * that same page load could not possibly pass, and the lead was lost silently.
 */
function sendJsonResponse($success, $message, $status = 200, ?string $csrfToken = null) {
    http_response_code($status);
    header('Content-Type: application/json');

    $payload = ['success' => $success, 'message' => $message];
    if ($csrfToken !== null) {
        $payload['csrf_token'] = $csrfToken;
    }
    $payload['antibot'] = form_challenge_reissue();

    echo json_encode($payload);
    exit;
}

/**
 * Renders a styled failure page for the non-AJAX path.
 *
 * This replaces a bare die('Security Exception: …'), which showed an unstyled
 * white page with a string that reads like a hacking accusation. The most
 * common way to reach it is an expired session on a form left open in a tab —
 * an ordinary visitor doing nothing wrong, who deserves a way back.
 */
function failPage(string $heading, string $message, int $status = 400): void
{
    http_response_code($status);

    $page = [
        'id'        => '',
        'title'     => $heading . ' | ' . SITE_NAME,
        'desc'      => $message,
        'bodyClass' => 'page-notice',
        'noindex'   => true,
    ];
    require __DIR__ . '/partials/head.php';
    require __DIR__ . '/partials/header.php';
    ?>
    <main id="main">
        <section class="section page-head">
            <div class="container container-narrow">
                <p class="eyebrow">Form not submitted</p>
                <h1><?= e($heading) ?></h1>
                <p class="lead"><?= e($message) ?></p>
                <div class="cluster" style="margin-top:2rem">
                    <a class="btn btn-pill" href="/contact">Back to the form <?= icon('arrow-right') ?></a>
                    <a class="btn btn-line" target="_blank" rel="noopener"
                       href="<?= e(whatsapp_link('Hi Rafly team, I tried the website form and it did not go through.')) ?>">
                        <?= icon('whatsapp', 'icon-fill') ?> Message us on WhatsApp
                    </a>
                </div>
            </div>
        </section>
    </main>
    <?php
    require __DIR__ . '/partials/tail.php';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /");
    exit();
}

// The CSRF token is guaranteed to exist — bootstrap.php generates it.

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    if ($isAjax) {
        sendJsonResponse(false, 'Your session expired. Please refresh the page and try again.', 403);
    }
    failPage(
        'Your session expired',
        'For security, the form is only valid for one session. Nothing was sent — please go back, refresh the page and submit again.',
        403
    );
}

/**
 * Honeypot. Answer 200/success so the bot believes it succeeded and moves on;
 * telling it that it was detected only teaches the operator to fix their script.
 * Nothing is written and nobody is notified.
 */
if (honeypot_tripped()) {
    error_log('Rafly: honeypot tripped, submission discarded.');
    if ($isAjax) {
        sendJsonResponse(true, 'Submitted successfully');
    }
    $_SESSION['lead_ok'] = true;
    header('Location: /thank-you', true, 303);
    exit;
}

/**
 * Unlike the honeypot, neither of these two is a certain bot signal — a real
 * visitor could in principle trip either — so both return a normal, visible
 * error rather than a fake success. Silently discarding a genuine submission
 * loses a real lead with no way for the visitor to know to retry; that is a
 * worse outcome than the rare false positive asking someone to try again.
 */
if (!form_timing_ok()) {
    if ($isAjax) {
        sendJsonResponse(false, 'That went through faster than a person filling in the form. Please try again.', 400);
    }
    failPage(
        'That was almost too quick',
        'Please go back and submit the form again.',
        400
    );
}

if (!antibot_check($_POST['antibot_answer'] ?? null)) {
    if ($isAjax) {
        sendJsonResponse(false, 'That answer was not correct. Please check the new sum and try again.', 400);
    }
    failPage(
        'That answer was not correct',
        'Please go back, check the sum and submit the form again.',
        400
    );
}

// Session-keyed first (catches accidental double-submits without punishing
// shared-NAT visitors), then IP-keyed — the one rate_limit_ok() cannot do on
// its own, since a client that discards its session defeats it entirely.
// Looser than the session limit on purpose: many people can legitimately
// submit from one office/carrier address.
if (!rate_limit_ok('lead', 5, 3600) || !rate_limit_ip_ok('lead', 15, 3600)) {
    if ($isAjax) {
        sendJsonResponse(false, 'You have sent several requests already. Please give us a little time to reply, or message us on WhatsApp.', 429);
    }
    failPage(
        'You have already sent us a few requests',
        'We have your details and will be in touch. If it is urgent, message us on WhatsApp rather than submitting the form again.',
        429
    );
}

if (!isset($_POST['contact_name'], $_POST['contact_email'], $_POST['company_name'], $_POST['contact_number'], $_POST['description'])) {
    if ($isAjax) {
        sendJsonResponse(false, 'Missing form fields.', 400);
    }
    failPage(
        'Some details are missing',
        'A required field did not come through. Please go back and fill in the form again.',
        400
    );
}

if (empty($_POST['consent']) || $_POST['consent'] !== 'on') {
    if ($isAjax) {
        sendJsonResponse(false, 'You must agree to the Privacy Policy before submitting this form.', 400);
    }
    failPage(
        'Consent is required',
        'Please go back and check the Privacy Policy consent box before submitting the form.',
        400
    );
}

$contact_name   = trim((string)($_POST['contact_name'] ?? ''));
$contact_email  = trim((string)($_POST['contact_email'] ?? ''));
$company_name   = trim((string)($_POST['company_name'] ?? ''));
$contact_number = trim((string)($_POST['contact_number'] ?? ''));
$description    = trim((string)($_POST['description'] ?? ''));

$contact_name   = strip_tags($contact_name);
$contact_email  = strip_tags($contact_email);
$company_name   = strip_tags($company_name);
$contact_number = strip_tags($contact_number);
$description    = strip_tags($description);

$contact_name   = preg_replace('/[\r\n\t]+/', ' ', $contact_name);
$contact_email  = preg_replace('/[\r\n\t]+/', ' ', $contact_email);
$company_name   = preg_replace('/[\r\n\t]+/', ' ', $company_name);
$contact_number = preg_replace('/[\r\n\t]+/', ' ', $contact_number);
$description    = preg_replace('/[\r\n\t]+/', ' ', $description);

$contact_name   = str_cut($contact_name, 120);
$contact_email  = str_cut($contact_email, 255);
$company_name   = str_cut($company_name, 100);
$contact_number = str_cut($contact_number, 30);
$description    = str_cut($description, 2000);

$contact_number = preg_replace('/[^0-9+()\-\s]/', '', $contact_number);

if ($contact_name === '' || $contact_email === '' || $company_name === '' || $contact_number === '' || $description === '') {
    if ($isAjax) {
        sendJsonResponse(false, 'Please provide all required details.', 400);
    }
    failPage(
        'Some details are missing',
        'Please go back and fill in your name, email, company name, contact number and requirements.',
        400
    );
}

if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    if ($isAjax) {
        sendJsonResponse(false, 'Please provide a valid email address.', 400);
    }
    failPage(
        'That email address does not look right',
        'Please go back and check the email address you entered.',
        400
    );
}

// Length is capped by str_cut() above, which counts CHARACTERS. This guard is a
// belt-and-braces re-check and must count characters too — a byte-based strlen()
// wrongly rejected legitimate non-Latin input, since a Hindi/Bengali character is
// 3 bytes, so a ~35-character company name already exceeded a 100-BYTE limit.
$len = static fn(string $s): int => function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s);
if ($len($contact_name) > 120 || $len($contact_email) > 255
    || $len($company_name) > 100 || $len($contact_number) > 30 || $len($description) > 2000) {
    if ($isAjax) {
        sendJsonResponse(false, 'One of the fields is too long. Please shorten it and try again.', 400);
    }
    failPage(
        'One of your answers is too long',
        'Please go back, shorten your answer, and submit the form again.',
        400
    );
}

$timestamp = date('Y-m-d H:i:s');

// Storage lives ABOVE the web root (inc/config.php). Previously this wrote to
// ./private/leads.csv, which on a default Apache host was publicly downloadable
// customer PII and was one commit away from being pushed to a public repo.
$storageDir = LEAD_STORE_PATH;
if (!is_dir($storageDir)) {
    // 0700, not 0755 — nothing but the PHP process has any business reading leads.
    if (!mkdir($storageDir, 0700, true) && !is_dir($storageDir)) {
        error_log('Rafly: lead storage directory could not be created at ' . $storageDir);
        if ($isAjax) {
            sendJsonResponse(false, 'We could not save your request. Please try again or contact us on WhatsApp.', 500);
        }
        failPage(
            'We could not save your request',
            'Something went wrong on our side. Please try again, or message us on WhatsApp so nothing is lost.',
            500
        );
    }
}

$file = LEAD_CSV_FILE;
$file_exists = file_exists($file);

$fp = fopen($file, 'ab');
if ($fp === false) {
    error_log('Rafly: unable to open lead file for writing at ' . $file);
    if ($isAjax) {
        sendJsonResponse(false, 'We could not save your request. Please try again or contact us on WhatsApp.', 500);
    }
    failPage(
        'We could not save your request',
        'Something went wrong on our side. Please try again, or message us on WhatsApp so nothing is lost.',
        500
    );
}

// Exclusive lock: two visitors submitting at the same moment would otherwise
// interleave their rows and corrupt the CSV.
$stored = false;

if (flock($fp, LOCK_EX)) {
    if (!$file_exists) {
        fputcsv($fp, array('Timestamp', 'Name', 'Email', 'Company Name', 'Contact Number', 'Requirements Description'));
    }
    // csv_safe() neutralises leading =, +, - and @, which Excel and Sheets would
    // otherwise execute as a live formula when staff open the file.
    //
    // The phone is deliberately NOT escaped: it has already been filtered to
    // [0-9+()\-\s], so it cannot contain a function name and cannot express a
    // dangerous formula. Escaping it would tab-prefix every single lead, since
    // effectively all of them start "+91". Email has no such filter (a valid
    // address can legitimately contain +/-/=), so it goes through csv_safe()
    // like every other free-text field.
    fputcsv($fp, array(
        $timestamp,
        csv_safe($contact_name),
        csv_safe($contact_email),
        csv_safe($company_name),
        $contact_number,
        csv_safe($description),
    ));
    fflush($fp);
    flock($fp, LOCK_UN);
    $stored = true;
}
fclose($fp);

if (!$stored) {
    error_log('Rafly: could not acquire lock on ' . $file . '; lead not written.');
    if ($isAjax) {
        sendJsonResponse(false, 'We could not save your request. Please try again or contact us on WhatsApp.', 500);
    }
    failPage(
        'We could not save your request',
        'Something went wrong on our side and your request was not stored. Please try again, or message us on WhatsApp so nothing is lost.',
        500
    );
}

/**
 * Persist to the leads table — the admin's source of truth.
 *
 * The CSV above is now an offline backup; the admin Leads screen and dashboard
 * read from this table. The lead is ALREADY safely stored on disk and about to
 * be emailed, so a database failure must not break the submission — it is logged
 * and swallowed, and db_available() means an unconfigured/unreachable database
 * degrades to CSV-only rather than throwing. created_at, updated_at and status
 * all take their column defaults (now / 'new').
 */
if (db_available()) {
    // Attribution supplied by lead_context_fields(); re-validated here because it
    // arrives from the client. source_page is sanitised for storage; service_slug
    // is only accepted if it is a real service.
    $source_page  = str_cut(preg_replace('/\p{Cc}+/u', '', (string)($_POST['source_page'] ?? '')) ?? '', 255);
    $service_slug = (string)($_POST['service_slug'] ?? '');
    if ($service_slug !== '' && !(defined('SERVICES') && array_key_exists($service_slug, SERVICES))) {
        $service_slug = '';
    }

    // A salted, one-way hash — enough to spot repeat submissions without keeping
    // the raw address. SITE_DOMAIN is a stable per-install salt.
    $ip_hash    = hash('sha256', (string)($_SERVER['REMOTE_ADDR'] ?? '') . '|' . SITE_DOMAIN);
    $user_agent = str_cut((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 500);

    try {
        insert_returning_id(
            'INSERT INTO leads
                (contact_name, contact_email, company_name, contact_number, description,
                 consent_given, source_page, service_slug, ip_hash, user_agent)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$contact_name, $contact_email, $company_name, $contact_number, $description,
             true, $source_page, $service_slug, $ip_hash, $user_agent]
        );
    } catch (Throwable $e) {
        // The lead is not lost — it is in the CSV and the notification email.
        error_log('Rafly: lead saved to CSV but DB insert failed: ' . $e->getMessage());
    }
}

/**
 * Rotate the CSRF token now that it has been spent. Without this, a token stays
 * valid for the life of the session and can be replayed indefinitely.
 */
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

/**
 * And re-seed the single-use anti-spam gates, for the same reason the failure
 * paths do — the visitor may well submit the consultation modal after the page
 * form, from the same page load.
 */
$freshChallenge = form_challenge_reissue();

if ($isAjax) {
    /**
     * Deliberately NOT routed through sendJsonResponse(): that helper calls
     * exit, which would kill this script before the deferred
     * send_lead_notification() below ever runs. The response body is identical.
     */
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode([
        'success'    => true,
        'message'    => 'Thanks — we have your details and will reply within one working day.',
        'csrf_token' => $_SESSION['csrf_token'],
        'antibot'    => $freshChallenge,
    ]);
} else {
    // Post/Redirect/Get: without this, refreshing the success page re-submits
    // the form and appends a duplicate lead row.
    $_SESSION['lead_ok'] = true;
    header('Location: /thank-you', true, 303);
}

/**
 * Everything the visitor's request is waiting on has already happened — the
 * lead is on disk (CSV + DB) and the response above is built. Only the email
 * notification is left, and its whole value is internal (staff get pinged);
 * the visitor gets nothing extra from waiting on a slow or unreachable SMTP
 * handshake. fastcgi_finish_request() (PHP-FPM, and LiteSpeed via LSAPI)
 * closes the connection to the client now while this script keeps running.
 * Where it's unavailable (this dev server has no FPM), the notification still
 * sends — just synchronously, same as before this change.
 */
if (function_exists('fastcgi_finish_request')) {
    session_write_close();
    fastcgi_finish_request();
} elseif (ob_get_level() > 0) {
    ob_end_flush();
    flush();
}

/**
 * Notify. The lead is already safely on disk, so a mail failure is logged and
 * swallowed — surfacing it would make the visitor re-submit and duplicate a row
 * that saved correctly. (The visitor already has their response either way.)
 */
send_lead_notification([
    'timestamp'   => $timestamp,
    'name'        => $contact_name,
    'email'       => $contact_email,
    'company'     => $company_name,
    'phone'       => $contact_number,
    'description' => $description,
]);

exit;
