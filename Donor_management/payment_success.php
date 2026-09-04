<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

if (!isset($_SESSION['donation'])) {
    die('No donation found.');
}

$d = $_SESSION['donation'];

try {

    $stmt = $conn->prepare("
        INSERT INTO donations 
        (donor_id, campaign_id, amount, donation_date, payment_method, payment_status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $d['donor_id'],
        $d['campaign_id'],
        $d['amount'],
        $d['donation_date'],
        'card',
        'completed'
    ]);

    $reference = 'DON-' . date('Ymd') . '-' . rand(10000,99999);

    unset($_SESSION['donation']);

} catch (Exception $e) {
    die('Error saving donation: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment Successful</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Donation Receipt</h1>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<div class="success-wrapper">

<div class="success-card">

<div class="success-icon">✓</div>

<h2>Payment Successful</h2>

<p>Thank you for your donation.</p>

<div class="reference">
    Transaction Reference: <?= $reference ?>
</div>

<div class="summary-box">

<h3>Transaction Summary</h3>

<p><b>Amount:</b> R<?= number_format($d['amount'],2) ?></p>
<p><b>Date:</b> <?= htmlspecialchars($d['donation_date']) ?></p>

<p><b>Status:</b> <span class="badge">Completed</span></p>

</div>

<a class="btn" href="donations.php">View All Donations</a>

</div>

</div>

</div>

<footer>
<p>© 2026 Save the Children South Africa</p>
</footer>

</body>
</html>