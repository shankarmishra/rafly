<?php
/**
 * RAFly Team OS — Channels & Team Communication Hub
 * Supports live channel chat, real-time SSE stream, and Message-to-Task conversion.
 */
require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require __DIR__ . '/lib/layout.php';

$channels = all('SELECT * FROM channels ORDER BY name ASC');
if (empty($channels)) {
    // Seed default channels if empty
    q("INSERT INTO channels (name, slug, type) VALUES ('General', 'general', 'topic'), ('Sales', 'sales', 'topic'), ('Development', 'development', 'topic'), ('Creative Studio', 'creative-studio', 'topic'), ('Security Ops', 'security-ops', 'topic')");
    $channels = all('SELECT * FROM channels ORDER BY name ASC');
}

$activeChannelId = (int)($_GET['channel_id'] ?? $channels[0]['id']);
$activeChannel   = one('SELECT * FROM channels WHERE id = ?', [$activeChannelId]);
if (!$activeChannel) {
    $activeChannelId = (int)$channels[0]['id'];
    $activeChannel   = $channels[0];
}

// POST Handling: Send Message or Convert Message to Task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_csrf();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'send_message') {
        $msgText = trim((string)($_POST['message'] ?? ''));
        if ($msgText !== '') {
            $currentUserId = current_user_id();
            q('INSERT INTO channel_messages (channel_id, user_id, message) VALUES (?, ?, ?)',
              [$activeChannelId, $currentUserId, $msgText]);
        }
        admin_redirect('/admin/channels.php?channel_id=' . $activeChannelId, 'Message posted.');
    }

    if ($action === 'convert_to_task') {
        $msgId = (int)($_POST['message_id'] ?? 0);
        $msgRow = one('SELECT * FROM channel_messages WHERE id = ?', [$msgId]);
        
        if ($msgRow) {
            $creatorId = current_user_id();
            $taskTitle = str_cut($msgRow['message'], 100);
            
            $taskId = insert_returning_id('INSERT INTO tasks (title, description, status, priority, creator_id) VALUES (?, ?, ?, ?, ?)',
                ['Task: ' . $taskTitle, $msgRow['message'], 'backlog', 'medium', $creatorId]);
            
            q('UPDATE channel_messages SET is_task_converted = true WHERE id = ?', [$msgId]);
            admin_redirect('/admin/tasks.php?id=' . $taskId, 'Task created from channel message.');
        }
        admin_redirect('/admin/channels.php?channel_id=' . $activeChannelId, 'Failed to convert message.', 'error');
    }
}

$messages = all('SELECT m.*, u.name as sender_name FROM channel_messages m JOIN users u ON m.user_id = u.id WHERE m.channel_id = ? ORDER BY m.created_at ASC', [$activeChannelId]);
$maxMsgId = !empty($messages) ? max(array_column($messages, 'id')) : 0;

admin_head(['title' => 'Channels Hub — RAFly Team OS', 'active' => '/admin/channels.php']);
?>
<div class="container">
    <div class="sec-head-split">
        <div>
            <p class="eyebrow">RAFly Team OS</p>
            <h1 class="display">Channels <span class="soft">Hub</span></h1>
            <p class="lead">Team collaboration with instant Message &rarr; Task conversion and SSE streams.</p>
        </div>
    </div>

    <div class="grid grid-4" style="margin-top:24px; gap:20px;">
        <div class="card" style="padding:16px;">
            <h4 style="margin:0 0 12px;">Channels</h4>
            <ul style="list-style:none; padding:0; margin:0;">
                <?php foreach ($channels as $ch): ?>
                    <li style="margin-bottom:8px;">
                        <a href="<?= e(site_path('/admin/channels.php?channel_id=' . $ch['id'])) ?>" class="btn btn-sm <?= (int)$ch['id'] === $activeChannelId ? 'btn-primary' : 'btn-line' ?>" style="width:100%; text-align:left;">
                            # <?= e($ch['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="card" style="grid-column: span 3; padding:20px; display:flex; flex-direction:column; min-height:480px;">
            <div style="border-bottom:1px solid #CBD5E1; padding-bottom:12px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 style="margin:0;"># <?= e($activeChannel['name']) ?></h3>
                    <p class="soft" style="margin:4px 0 0; font-size:13px;">Topic Channel &middot; Realtime updates active</p>
                </div>
                <span class="badge badge-ok" id="sse-status" style="font-size:11px;">SSE Live</span>
            </div>

            <div id="messages-container" style="flex:1; overflow-y:auto; margin-bottom:16px; display:flex; flex-direction:column; gap:12px;">
                <?php if (empty($messages)): ?>
                    <p class="soft text-center" style="margin-top:40px;">No messages in this channel yet. Start the conversation!</p>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div style="background:#F8FAFC; padding:12px; border-radius:6px; border:1px solid #E2E8F0;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <strong><?= e($msg['sender_name']) ?></strong>
                                <span class="soft" style="font-size:11px;"><?= e(date('M d, H:i', strtotime($msg['created_at']))) ?></span>
                            </div>
                            <p style="margin:6px 0; font-size:14px; color:#1E293B;"><?= nl2br(e($msg['message'])) ?></p>
                            <div style="display:flex; gap:8px; align-items:center; margin-top:6px;">
                                <?php if (!empty($msg['is_task_converted'])): ?>
                                    <span class="badge badge-ok" style="font-size:10px;">Converted to Task</span>
                                <?php else: ?>
                                    <form method="post" action="channels.php?channel_id=<?= (int)$activeChannelId ?>" style="margin:0;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="convert_to_task">
                                        <input type="hidden" name="message_id" value="<?= (int)$msg['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-secondary" style="font-size:11px; padding:2px 8px;">+ Convert to Task</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form method="post" action="channels.php?channel_id=<?= (int)$activeChannelId ?>" style="border-top:1px solid #E2E8F0; padding-top:12px; display:flex; gap:8px;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="send_message">
                <input type="text" name="message" placeholder="Type a message in #<?= e($activeChannel['name']) ?>..." required style="flex:1; padding:10px 14px; border-radius:6px; border:1px solid #CBD5E1; font-size:14px;">
                <button type="submit" class="btn btn-primary" style="padding:10px 20px;">Send</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var channelId = <?= (int)$activeChannelId ?>;
    var lastId = <?= (int)$maxMsgId ?>;
    
    if (!!window.EventSource && channelId > 0) {
        var source = new EventSource('<?= e(site_path('/admin/api/sse.php')) ?>?channel_id=' + channelId + '&last_id=' + lastId);
        
        source.addEventListener('channel_message', function(e) {
            var data = JSON.parse(e.data);
            if (data.id > lastId) {
                location.reload();
            }
        });
    }
});
</script>

<?php
admin_foot();
