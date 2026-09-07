<?php
/**
 * RAFly Lead Automation Webhook Handler (SOP 29 & SOP 10)
 * Architecture: Web Form Submission -> Webhook Validation -> CRM Entry -> WhatsApp Auto-Ack Link -> Sales Alert
 */

require_once __DIR__ . '/../inc/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data     = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

$contactName  = trim((string)($data['contact_name'] ?? ''));
$contactEmail = trim((string)($data['contact_email'] ?? ''));
$companyName  = trim((string)($data['company_name'] ?? ''));
$contactPhone = trim((string)($data['contact_number'] ?? $data['contact_phone'] ?? ''));
$description  = trim((string)($data['description'] ?? ''));
$sourcePage   = trim((string)($data['source_page'] ?? 'webhook'));

if ($contactName === '' || $companyName === '' || $contactPhone === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields (contact_name, company_name, contact_number)']);
    exit;
}

// Perform initial qualification scoring (SOP 10)
$score = 0;
if (!empty($companyName)) $score += 20; // Legitimacy
if (str_contains(strtolower($description), 'budget') || str_contains(strtolower($description), 'lakh') || str_contains(strtolower($description), '25000')) $score += 30; // Budget
if (str_contains(strtolower($description), 'urgent') || str_contains(strtolower($description), 'week') || str_contains(strtolower($description), 'asap')) $score += 20; // Urgency
$score += 15; // Tech fit default

$stage = ($score >= 70) ? 'qualified' : 'new';
$status = ($score >= 70) ? 'qualified' : 'new';

$leadId = 0;
if (db_available()) {
    $leadId = insert_returning_id('
        INSERT INTO leads (contact_name, contact_email, company_name, contact_number, description, source_page, status, deal_stage, qualification_score, consent_given)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, true)
    ', [$contactName, $contactEmail, $companyName, $contactPhone, $description, $sourcePage, $status, $stage, $score]);
}

// Generate 60-second WhatsApp Auto-Ack message
$waText = "Hi " . $contactName . ", thank you for reaching out to RAFly Digital Growth regarding " . $companyName . ". We have received your enquiry and assigned it to our growth team. Book your 15-min discovery call here: " . SITE_ORIGIN . "/pricing";
$waLink = whatsapp_link_to($contactPhone, $waText);

echo json_encode([
    'success' => true,
    'lead_id' => $leadId,
    'qualification_score' => $score,
    'fit_tier' => ($score >= 70) ? 'HIGH_FIT' : (($score >= 40) ? 'MEDIUM_FIT' : 'LOW_FIT'),
    'whatsapp_auto_ack_link' => $waLink,
    'message' => 'Lead processed and automated qualification workflow executed.'
]);
exit;
