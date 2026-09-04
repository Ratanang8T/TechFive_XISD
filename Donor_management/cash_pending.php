<?php
require_once 'auth.php';
require_admin();
require_once 'db.php';

/* =========================
   APPROVE CASH
========================= */
if (isset($_POST['approve'], $_POST['donation_id'])) {

    $stmt = $conn->prepare("
        UPDATE donations
        SET payment_status = 'completed'
        WHERE donation_id = ?
        AND payment_method = 'cash'
    ");

    $stmt->execute([$_POST['donation_id']]);

    header("Location: cash_pending.php");
    exit();
}

/* =========================
   GET PENDING CASH DONATIONS
========================= */
$stmt = $conn->query("
    SELECT donations.donation_id,
           donations.amount,
           donations.donation_date,
           donors.name,
           campaigns.campaign_name
    FROM donations
    JOIN donors ON donations.donor_id = donors.donor_id
    JOIN campaigns ON donations.campaign_id = campaigns.campaign_id
    WHERE donations.payment_method = 'cash'
    AND donations.payment_status = 'pending'
    ORDER BY donations.donation_date DESC
");

$cashDonations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Cash Approvals</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
<h1>Cash Deposit Approvals</h1>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<div class="card">

<h2>Pending Cash Donations</h2>

<table width="100%" border="1" cellpadding="10" cellspacing="0">

<tr>
    <th>Donor</th>
    <th>Campaign</th>
    <th>Amount</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php if (count($cashDonations) > 0): ?>

    <?php foreach ($cashDonations as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= htmlspecialchars($c['campaign_name']) ?></td>
        <td>R<?= number_format($c['amount'],2) ?></td>
        <td><?= htmlspecialchars($c['donation_date']) ?></td>

        <td>
            <form method="POST">
                <input type="hidden" name="donation_id" value="<?= $c['donation_id'] ?>">
                <button type="submit" name="approve">
                    Approve
                </button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>

<?php else: ?>

    <tr>
        <td colspan="5" style="text-align:center;">
            No pending cash donations
        </td>
    </tr>

<?php endif; ?>

</table>

</div>

</div>

<footer>
<p>© 2026 Save the Children South Africa</p>
</footer>

</body>
</html>