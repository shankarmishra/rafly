<?php
/**
 * Lead notification.
 *
 * Before this existed, a submission was appended to a CSV above the web root
 * and nobody was told. Every enquiry sat unread until someone remembered to
 * SFTP in and look — which for a lead-generation site meant losing business
 * silently, the worst possible failure mode.
 *
 * Design rule: the CSV remains the source of truth. Mail is a NOTIFICATION.
 * A mail failure must never fail the submission, because the lead is already
 * safely stored by the time we get here — telling the visitor "something went
 * wrong" would make them re-submit and duplicate a row that saved fine.
 */

/**
 * Sends the lead notification. Returns false on failure; the caller is expected
 * to log and carry on rather than surface anything to the visitor.
 *
 * @param array{timestamp:string,name:string,email:string,company:string,phone:string,description:string} $lead
 */
function send_lead_notification(array $lead): bool
{
    if (!function_exists('mail')) {
        error_log('Rafly: mail() unavailable — lead notification skipped.');
        return false;
    }

    $to = LEAD_NOTIFY_EMAIL;

    // The company name is visitor-supplied and goes in the SUBJECT, so it must
    // be stripped of CR/LF or it becomes a header-injection vector that lets an
    // attacker add Bcc: recipients and turn this form into a spam relay.
    $safeCompany = mail_header_safe($lead['company']);
    $subject     = 'New enquiry: ' . str_cut($safeCompany, 60);

    $body = implode("\n", [
        'A new enquiry was submitted on ' . SITE_DOMAIN . '.',
        '',
        'Received:  ' . $lead['timestamp'],
        'Name:      ' . $lead['name'],
        'Email:     ' . $lead['email'],
        'Company:   ' . $lead['company'],
        'Phone:     ' . $lead['phone'],
        '',
        'Requirements',
        '------------',
        $lead['description'],
        '',
        '---',
        'Stored in ' . basename(LEAD_CSV_FILE) . '. This message is a notification only;',
        'the CSV above the web root remains the authoritative record.',
    ]);

    // From must be a domain we control, or the message fails SPF/DMARC and is
    // filed as spam. Reply-To is still the internal notify address, not the
    // visitor's — a staff reply must not silently go to a stranger because
    // they clicked "Reply" without checking.
    $headers = implode("\r\n", [
        'From: ' . SITE_NAME . ' Website <no-reply@' . SITE_DOMAIN . '>',
        'Reply-To: ' . LEAD_NOTIFY_EMAIL,
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: PHP/' . PHP_VERSION,
        'Auto-Submitted: auto-generated',
    ]);

    $sent = @mail($to, $subject, $body, $headers);

    if (!$sent) {
        error_log('Rafly: lead notification to ' . $to . ' failed to send.');
    }

    return (bool)$sent;
}

/**
 * Strips everything that could break out of a mail header.
 * Covers CR, LF, and the bare NUL that some MTAs treat as a terminator.
 */
function mail_header_safe(string $value): string
{
    return trim(preg_replace('/[\r\n\0]+/', ' ', $value));
}
