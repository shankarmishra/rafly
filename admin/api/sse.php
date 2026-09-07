<?php
/**
 * RAFly Team OS — Server-Sent Events (SSE) Real-time Stream
 * Pushes live updates for channel messages and notifications.
 */

require_once __DIR__ . '/../lib/bootstrap.php';
require_can('content.view');

// Prevent output buffering and session lock blocking
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Disable Nginx buffering

$channelId = isset($_GET['channel_id']) ? (int)$_GET['channel_id'] : 0;
$lastSeenId = isset($_SERVER['HTTP_LAST_EVENT_ID']) ? (int)$_SERVER['HTTP_LAST_EVENT_ID'] : (int)($_GET['last_id'] ?? 0);

// Flush initial connection headers
echo ": ping\n\n";
if (ob_get_level() > 0) {
    ob_flush();
}
flush();

// Query for new messages in target channel
if ($channelId > 0) {
    $newMessages = all('
        SELECT m.id, m.channel_id, m.message, m.created_at, u.name as sender_name 
          FROM channel_messages m 
          JOIN users u ON m.user_id = u.id 
         WHERE m.channel_id = ? AND m.id > ? 
         ORDER BY m.id ASC
    ', [$channelId, $lastSeenId]);

    foreach ($newMessages as $msg) {
        echo "id: " . (int)$msg['id'] . "\n";
        echo "event: channel_message\n";
        echo "data: " . json_encode([
            'id' => (int)$msg['id'],
            'channel_id' => (int)$msg['channel_id'],
            'sender_name' => $msg['sender_name'],
            'message' => $msg['message'],
            'created_at' => date('M d, H:i', strtotime($msg['created_at'])),
        ]) . "\n\n";
    }
    
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}
exit;
