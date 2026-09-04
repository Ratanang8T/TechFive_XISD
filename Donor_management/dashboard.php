<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

/* =====================
   SAFE STATS (NO BROKEN COLUMNS)
===================== */
$totalDonors = $conn->query('SELECT COUNT(*) FROM donors')->fetchColumn();
$totalCampaigns = $conn->query('SELECT COUNT(*) FROM campaigns')->fetchColumn();
$totalMessages = $conn->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$totalDonations = $conn->query('SELECT COUNT(*) FROM donations')->fetchColumn();
$totalRaised = $conn->query('SELECT COALESCE(SUM(amount),0) FROM donations')->fetchColumn();

/* =====================
   RECENT DONATIONS
===================== */
$recentDonations = $conn->query("
    SELECT
        donors.name,
        campaigns.campaign_name,
        donations.amount,
        donations.donation_date
    FROM donations
    JOIN donors ON donations.donor_id = donors.donor_id
    JOIN campaigns ON donations.campaign_id = campaigns.campaign_id
    ORDER BY donations.donation_date DESC, donations.donation_id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

/* =====================
   CAMPAIGN PROGRESS (SAFE)
===================== */
$campaignProgress = $conn->query("
    SELECT
        campaigns.campaign_id,
        campaigns.campaign_name,
        campaigns.fundraising_goal,
        COALESCE(SUM(donations.amount),0) AS raised
    FROM campaigns
    LEFT JOIN donations
        ON campaigns.campaign_id = donations.campaign_id
    GROUP BY campaigns.campaign_id, campaigns.campaign_name, campaigns.fundraising_goal
")->fetchAll(PDO::FETCH_ASSOC);

/* =====================
   ACTIVITY FEED (SAFE VERSION)
===================== */
$activityFeed = $conn->query("
    SELECT 'Donation' AS type,
           CONCAT(donors.name,' donated R', donations.amount) AS message,
           donations.donation_date AS date
    FROM donations
    JOIN donors ON donations.donor_id = donors.donor_id

    UNION ALL

    SELECT 'Campaign' AS type,
           CONCAT('New campaign: ', campaign_name) AS message,
           created_at AS date
    FROM campaigns

    ORDER BY date DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">

<style>

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
    margin-bottom:20px;
}

/* STATS CARDS */
.card-box{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    text-align:center;
}

.card-box h2{
    color:#3498db;
    margin:5px 0;
}

/* SECTIONS */
.section{
    margin-top:20px;
}

/* PROGRESS */
.progress-bar{
    width:100%;
    background:#e0e0e0;
    height:16px;
    border-radius:20px;
    overflow:hidden;
}

.progress-fill{
    height:100%;
    background:#27ae60;
    color:white;
    font-size:12px;
    text-align:center;
    line-height:16px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

table th, table td{
    border:1px solid #ddd;
    padding:10px;
}

table th{
    background:#f4f6f9;
}

/* ACTIVITY */
.activity{
    background:white;
    padding:12px;
    margin-bottom:10px;
    border-radius:10px;
    box-shadow:0 2px 6px rgba(0,0,0,0.06);
}

.badge{
    display:inline-block;
    padding:3px 8px;
    border-radius:6px;
    font-size:12px;
    color:white;
}

.donation{background:#27ae60;}
.campaign{background:#3498db;}

</style>
</head>

<body>

<header>
    <h1>
        Welcome, <?= htmlspecialchars(current_user_name()) ?><br>
        <small>Role: <?= htmlspecialchars(current_role()) ?></small>
    </h1>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<!-- STATS -->
<div class="grid">

    <div class="card-box">
        <h4>Total Donors</h4>
        <h2><?= $totalDonors ?></h2>
    </div>

    <div class="card-box">
        <h4>Total Donations</h4>
        <h2><?= $totalDonations ?></h2>
    </div>

    <div class="card-box">
        <h4>Total Raised</h4>
        <h2>R<?= number_format($totalRaised,2) ?></h2>
    </div>

    <div class="card-box">
        <h4>Campaigns</h4>
        <h2><?= $totalCampaigns ?></h2>
    </div>

</div>

<!-- IMPACT SECTION -->
<div class="section card">

<h2>Campaign Impact</h2>

<?php foreach ($campaignProgress as $c): ?>

<?php
$goal = max(1, (float)$c['fundraising_goal']);
$raised = (float)$c['raised'];
$percent = min(100, round(($raised / $goal) * 100));
?>

<p><strong><?= htmlspecialchars($c['campaign_name']) ?></strong></p>

<div class="progress-bar">
    <div class="progress-fill" style="width: <?= $percent ?>%;">
        <?= $percent ?>%
    </div>
</div>

<p>R<?= number_format($raised,2) ?> / R<?= number_format($goal,2) ?></p>

<hr>

<?php endforeach; ?>

</div>

<!-- ACTIVITY FEED -->
<div class="section card">

<h2>Recent Activity</h2>

<?php foreach ($activityFeed as $a): ?>

<div class="activity">

    <span class="badge <?= strtolower($a['type']) ?>">
        <?= $a['type'] ?>
    </span>

    <p><?= htmlspecialchars($a['message']) ?></p>
    <small><?= htmlspecialchars($a['date']) ?></small>

</div>

<?php endforeach; ?>

</div>

<!-- RECENT DONATIONS -->
<div class="section card">

<h2>Recent Donations</h2>

<table>
<thead>
<tr>
    <th>Donor</th>
    <th>Campaign</th>
    <th>Amount</th>
    <th>Date</th>
</tr>
</thead>

<tbody>

<?php foreach ($recentDonations as $d): ?>
<tr>
    <td><?= htmlspecialchars($d['name']) ?></td>
    <td><?= htmlspecialchars($d['campaign_name']) ?></td>
    <td>R<?= number_format($d['amount'],2) ?></td>
    <td><?= htmlspecialchars($d['donation_date']) ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>

</div>

<footer>
    <p>© 2026 Save the Children South Africa. All Rights Reserved.</p>
</footer>

</body>
</html>