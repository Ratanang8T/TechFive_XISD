<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['donation'])) {
    die("No donation found.");
}

$amount = number_format($_SESSION['donation']['amount'], 2);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Secure Checkout</title>
<link rel="stylesheet" href="style.css">

<style>

/* PAGE LAYOUT */
.payment-wrapper{
    max-width:520px;
    margin:40px auto;
}

.payment-card{
    background:white;
    border-radius:16px;
    padding:30px;
    box-shadow:0 10px 30px rgba(0,0,0,0.12);
}

/* HEADER */
.secure-header{
    text-align:center;
    margin-bottom:20px;
}

.secure-header h2{
    margin:0;
    color:#2c3e50;
}

.secure-header p{
    color:#777;
    font-size:14px;
}

/* CARD LOGOS (more realistic) */
.card-logos{
    display:flex;
    justify-content:center;
    gap:20px;
    font-size:14px;
    margin-bottom:20px;
}

.logo-box{
    border:1px solid #ddd;
    padding:8px 14px;
    border-radius:8px;
    font-weight:bold;
    color:#555;
    background:#f9f9f9;
}

/* SUMMARY */
.summary{
    background:#f5f7fa;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    margin-bottom:8px;
}

.summary-row strong{
    color:#2c3e50;
}

/* FORM */
label{
    font-size:13px;
    color:#444;
}

input{
    width:100%;
    padding:10px;
    margin:6px 0 12px 0;
    border:1px solid #ccc;
    border-radius:6px;
    font-size:14px;
}

input:focus{
    border-color:#3498db;
    outline:none;
}

/* PAY BUTTON */
.pay-btn{
    width:100%;
    padding:14px;
    font-size:16px;
    border:none;
    border-radius:10px;
    background:#27ae60;
    color:white;
    cursor:pointer;
    font-weight:bold;
}

.pay-btn:hover{
    background:#219150;
}

/* SPINNER */
.spinner-overlay{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.65);
    z-index:9999;
}

.spinner-content{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%, -50%);
    text-align:center;
    color:white;
}

.loader{
    border:6px solid rgba(255,255,255,0.3);
    border-top:6px solid white;
    border-radius:50%;
    width:60px;
    height:60px;
    animation:spin 1s linear infinite;
    margin:auto;
}

@keyframes spin{
    0%{transform:rotate(0deg);}
    100%{transform:rotate(360deg);}
}

.small-text{
    font-size:12px;
    color:#888;
    margin-top:10px;
}

</style>

<script>
function processPayment() {

    document.getElementById("spinner").style.display = "block";

    setTimeout(function(){
        document.getElementById("paymentForm").submit();
    }, 2500);

    return false;
}
</script>

</head>

<body>

<header>
    <h1>Secure Donation Checkout</h1>
</header>

<div class="container">

<div class="payment-wrapper">

<div class="payment-card">

<!-- HEADER -->
<div class="secure-header">
    <h2>🔒 Secure Payment Gateway</h2>
    <p>All transactions are encrypted (demo system)</p>
</div>

<!-- CARD LOGOS -->
<div class="card-logos">
    <div class="logo-box">VISA</div>
    <div class="logo-box">Mastercard</div>
</div>

<!-- SUMMARY -->
<div class="summary">

    <div class="summary-row">
        <span>Donation Amount</span>
        <strong>R<?= $amount ?></strong>
    </div>

    <div class="summary-row">
        <span>Processing Fee</span>
        <strong>R0.00</strong>
    </div>

    <hr>

    <div class="summary-row">
        <strong>Total</strong>
        <strong>R<?= $amount ?></strong>
    </div>

</div>

<!-- FORM -->
<form id="paymentForm"
      action="payment_success.php"
      method="POST"
      onsubmit="return processPayment();">

<label>Cardholder Name</label>
<input type="text" name="cardholder" placeholder="John Smith" required>

<label>Card Number</label>
<input type="text" name="cardnumber" placeholder="4111 1111 1111 1111" maxlength="19" required>

<label>Expiry Date</label>
<input type="text" name="expiry" placeholder="MM/YY" maxlength="5" required>

<label>CVV</label>
<input type="password" name="cvv" placeholder="123" maxlength="4" required>

<button class="pay-btn" type="submit">
    Pay R<?= $amount ?>
</button>

<div class="small-text">
    This is a demonstration payment system for academic use only.
</div>

</form>

</div>

</div>

</div>

<!-- SPINNER -->
<div id="spinner" class="spinner-overlay">

<div class="spinner-content">

<div class="loader"></div>

<h2>Processing Payment...</h2>
<p>Please wait while we securely process your donation.</p>

</div>

</div>

<footer>
<p>© 2026 Save the Children South Africa. All Rights Reserved.</p>
</footer>

</body>
</html>