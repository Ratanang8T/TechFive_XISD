<?php
require_once 'auth.php';
require_roles(['Administrator','Donor']);
require_once 'db.php';

$message = '';
$canAdmin = role_is('Administrator');

/* =========================
   CAMPAIGNS
========================= */
$campaigns = $conn->query("
    SELECT campaign_id, campaign_name
    FROM campaigns
    ORDER BY campaign_name
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   DONOR LINK
========================= */
if (role_is('Donor')) {

    $stmt = $conn->prepare("SELECT donor_id FROM donors WHERE email = ?");
    $stmt->execute([$_SESSION['email']]);
    $myDonor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$myDonor) {
        $stmt = $conn->prepare("
            INSERT INTO donors (name, email, phone, address)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['full_name'],
            $_SESSION['email'],
            '',
            ''
        ]);

        $donor_id = $conn->lastInsertId();
    } else {
        $donor_id = $myDonor['donor_id'];
    }

} else {
    $donors = $conn->query("
        SELECT donor_id, name FROM donors ORDER BY name
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/* =========================
   SUBMIT DONATION
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_donation'])) {

    $selectedDonor  = role_is('Donor') ? $donor_id : (int) ($_POST['donor_id'] ?? 0);
    $campaignId     = (int) ($_POST['campaign_id'] ?? 0);
    $amount         = filter_var($_POST['amount'] ?? '', FILTER_VALIDATE_FLOAT);
    $donationDate   = trim($_POST['donation_date'] ?? '');
    $paymentMethod  = in_array($_POST['payment_method'] ?? '', ['cash', 'card'])
                        ? $_POST['payment_method']
                        : 'card';

    // Validate inputs
    $dateObj   = DateTime::createFromFormat('Y-m-d', $donationDate);
    $validDate = $dateObj && $dateObj->format('Y-m-d') === $donationDate;

    if (!$campaignId) {
        $message = 'Please select a campaign.';
    } elseif ($amount === false || $amount <= 0) {
        $message = 'Please enter a valid donation amount greater than zero.';
    } elseif (!$validDate) {
        $message = 'Please enter a valid donation date.';
    } elseif (!$selectedDonor) {
        $message = 'Donor could not be determined. Please try again.';
    } else {
        $_SESSION['donation'] = [
            'donor_id'       => $selectedDonor,
            'campaign_id'    => $campaignId,
            'amount'         => $amount,
            'donation_date'  => $donationDate,
            'payment_method' => $paymentMethod
        ];

        if ($paymentMethod === 'cash') {
            header("Location: cash_instructions.php");
        } else {
            header("Location: payment_processing.php");
        }
        exit();
    }
}

/* =========================
   HISTORY
========================= */
if (role_is('Donor')) {

    $stmt = $conn->prepare("
        SELECT donations.*, campaigns.campaign_name
        FROM donations
        JOIN campaigns ON donations.campaign_id = campaigns.campaign_id
        WHERE donations.donor_id = ?
        ORDER BY donations.donation_date DESC
    ");

    $stmt->execute([$donor_id]);
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {

    $donations = $conn->query("
        SELECT donations.*, donors.name, campaigns.campaign_name
        FROM donations
        JOIN donors ON donations.donor_id = donors.donor_id
        JOIN campaigns ON donations.campaign_id = campaigns.campaign_id
        ORDER BY donations.donation_date DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/* =========================
   DELETE DONATION
========================= */
if ($canAdmin && isset($_POST['delete_donation'])) {

    $stmt = $conn->prepare("DELETE FROM donations WHERE donation_id = ?");
    $stmt->execute([$_POST['donation_id']]);

    header("Location: donations.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Donations</title>
<link rel="stylesheet" href="style.css">

<style>
.container-box{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.method-box{
    display:flex;
    gap:10px;
    margin:10px 0;
}

.method{
    flex:1;
    padding:12px;
    border:2px solid #ccc;
    border-radius:10px;
    cursor:pointer;
    text-align:center;
    transition:0.2s;
    background:white;
}

.method:hover{
    border-color:#3498db;
}

.method.active{
    border-color:#22c55e;
    background:#eafaf1;
}

.pay-tag{
    padding:5px 10px;
    border-radius:5px;
    font-size:12px;
}

.card-pay{background:#dfefff;color:#2c3e50;}
.cash-pay{background:#fff3cd;color:#856404;}

.delete-btn{
    background:#e74c3c;
    color:white;
    border:none;
    padding:6px 10px;
    border-radius:5px;
}

@media(max-width:900px){
    .container-box{grid-template-columns:1fr;}
}
</style>

</head>

<body>

<header>
<h1>Donations</h1>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<div class="container-box">

<!-- FORM -->
<div class="card">

<h2>Make Donation</h2>

<?php if ($message): ?>
<p class="error"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST">

<?php if (role_is('Administrator')): ?>
<label>Donor</label>
<select name="donor_id" required>
<?php foreach ($donors as $d): ?>
<option value="<?= $d['donor_id'] ?>">
<?= htmlspecialchars($d['name']) ?>
</option>
<?php endforeach; ?>
</select>
<?php endif; ?>

<label>Campaign</label>
<select name="campaign_id" required>
<?php foreach ($campaigns as $c): ?>
<option value="<?= $c['campaign_id'] ?>">
<?= htmlspecialchars($c['campaign_name']) ?>
</option>
<?php endforeach; ?>
</select>

<label>Amount</label>
<input type="number" name="amount" step="0.01" required>

<label>Date</label>
<input type="date" name="donation_date" required>

<input type="hidden" id="payment_method" name="payment_method" value="card">

<p><b>Select Payment Method</b></p>

<div class="method-box">

<div class="method active" onclick="selectMethod('card', this)">
💳 Card
</div>

<div class="method" onclick="selectMethod('cash', this)">
🏦 Cash Deposit
</div>

</div>

<button type="submit">Proceed</button>

</form>

</div>

<!-- HISTORY -->
<div class="card">

<h2>Donation History</h2>

<table width="100%">
<tr>
<?php if (role_is('Administrator')): ?>
<th>Donor</th>
<?php endif; ?>
<th>Campaign</th>
<th>Amount</th>
<th>Method</th>
<th>Date</th>
<?php if ($canAdmin): ?>
<th>Action</th>
<?php endif; ?>
</tr>

<?php foreach ($donations as $d): ?>
<tr>

<?php if (role_is('Administrator')): ?>
<td><?= htmlspecialchars($d['name']) ?></td>
<?php endif; ?>

<td><?= htmlspecialchars($d['campaign_name']) ?></td>
<td>R<?= number_format($d['amount'],2) ?></td>

<td>
<?php if (($d['payment_method'] ?? 'card') === 'cash'): ?>
<span class="pay-tag cash-pay">Cash</span>
<?php else: ?>
<span class="pay-tag card-pay">Card</span>
<?php endif; ?>
</td>

<td><?= htmlspecialchars($d['donation_date']) ?></td>

<?php if ($canAdmin): ?>
<td>
<form method="POST">
<input type="hidden" name="donation_id" value="<?= $d['donation_id'] ?>">
<button class="delete-btn" name="delete_donation">Delete</button>
</form>
</td>
<?php endif; ?>

</tr>
<?php endforeach; ?>

</table>

</div>

</div>

</div>

<script>
function selectMethod(value, element){

    document.getElementById('payment_method').value = value;

    document.querySelectorAll('.method').forEach(m => {
        m.classList.remove('active');
    });

    element.classList.add('active');
}
</script>

<footer>
<p>© 2026 Save the Children South Africa</p>
</footer>

</body>
</html>