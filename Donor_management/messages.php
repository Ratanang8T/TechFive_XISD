<?php
require_once 'auth.php'; require_staff_or_admin(); require_once 'db.php';
$messageText = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('INSERT INTO messages (subject, message, created_by) VALUES (?, ?, ?)');
    $stmt->execute([trim($_POST['subject']), trim($_POST['message']), $_SESSION['user_id']]);
    $messageText = 'Message saved successfully.';
}
$messages = $conn->query('SELECT messages.*, users.full_name FROM messages LEFT JOIN users ON messages.created_by=users.user_id ORDER BY messages.sent_date DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html><head><title>Messages</title><link rel="stylesheet" href="style.css"></head><body>
<header><h1>Send Message to Donors</h1></header><?php include 'nav.php'; ?>
<div class="container">
<?php if ($messageText): ?><p class="success"><?= htmlspecialchars($messageText) ?></p><?php endif; ?>
<div class="card"><form method="POST"><label>Subject</label><input type="text" name="subject" required><label>Message</label><textarea rows="5" name="message" required></textarea><button type="submit">Send Message</button></form></div>
<div class="card"><h2>Sent Messages</h2><table><thead><tr><th>Subject</th><th>Message</th><th>Sent By</th><th>Date</th></tr></thead><tbody>
<?php foreach ($messages as $m): ?><tr><td><?= htmlspecialchars($m['subject']) ?></td><td><?= nl2br(htmlspecialchars($m['message'])) ?></td><td><?= htmlspecialchars($m['full_name'] ?? 'System') ?></td><td><?= htmlspecialchars($m['sent_date']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<footer><p>© 2026 Save the Children South Africa. All Rights Reserved.</p></footer></body></html>
