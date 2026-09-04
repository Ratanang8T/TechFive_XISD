<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

if (!isset($_SESSION['donation'])) {
    die("No donation found.");
}

$d = $_SESSION['donation'];

try {

    $stmt = $conn->prepare(
        "INSERT INTO donations 
        (donor_id, campaign_id, amount, donation_date, payment_method, payment_status)
        VALUES (?, ?, ?, ?, 'cash', 'pending')"
    );

    $stmt->execute([
        $d['donor_id'],
        $d['campaign_id'],
        $d['amount'],
        $d['donation_date']
    ]);

    unset($_SESSION['donation']);

} catch (Exception $e) {
    die("Error saving cash donation: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Cash Donation Recorded</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Cash Donation Submitted</h1>
</header>

<div class="container">

<div class="card">

<h2>Thank You</h2>

<p>Your cash donation has been recorded successfully.</p>

<p>Status: <b>PENDING</b> (awaiting admin confirmation)</p>

<a href="donations.php">Back to Donations</a>

</div>

</div>

<footer>
<p>© 2026 Save the Children South Africa</p>
</footer>

</body>
</html>