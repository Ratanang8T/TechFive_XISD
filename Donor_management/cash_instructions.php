<?php
require_once 'auth.php';
require_login();

if (!isset($_SESSION['donation'])) {
    die("No donation found.");
}

$d = $_SESSION['donation'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Cash Deposit Instructions</title>
<link rel="stylesheet" href="style.css">

<style>
.box {
    max-width: 700px;
    margin: 30px auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.bank-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
}

.highlight {
    font-weight: bold;
    color: #2c3e50;
}

.btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 15px;
    background: #27ae60;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
</style>
</head>

<body>

<header>
    <h1>Cash Deposit Instructions</h1>
</header>

<div class="container">

<div class="box">

<h2>Complete Your Cash Donation</h2>

<p>Please deposit your donation using the banking details below.</p>

<div class="bank-box">

<p class="highlight">Bank Name:</p>
<p>Standard Bank</p>

<p class="highlight">Account Name:</p>
<p>Save the Children South Africa</p>

<p class="highlight">Account Number:</p>
<p>123 456 789</p>

<p class="highlight">Branch Code:</p>
<p>051001</p>

<p class="highlight">Reference:</p>
<p>DONATION-<?= rand(10000,99999) ?></p>

</div>

<hr>

<h3>Donation Summary</h3>

<p><b>Amount:</b> R<?= number_format($d['amount'],2) ?></p>
<p><b>Date:</b> <?= htmlspecialchars($d['donation_date']) ?></p>

<!-- IMPORTANT: go to confirm page -->
<a class="btn" href="confirm_cash.php">
    I Have Made the Deposit
</a>

</div>

</div>

<footer>
<p>© 2026 Save the Children South Africa</p>
</footer>

</body>
</html>