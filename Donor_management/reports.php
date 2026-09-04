<?php
require_once 'auth.php';
require_staff_or_admin();
require_once 'db.php';

/* =====================
   SUMMARY STATS
===================== */
$totalDonors = $conn->query('SELECT COUNT(*) FROM donors')->fetchColumn();
$totalCampaigns = $conn->query('SELECT COUNT(*) FROM campaigns')->fetchColumn();
$totalMessages = $conn->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$totalDonations = $conn->query('SELECT COUNT(*) FROM donations')->fetchColumn();
$totalRaised = $conn->query('SELECT COALESCE(SUM(amount),0) FROM donations')->fetchColumn();

/* =====================
   CAMPAIGN REPORT DATA
===================== */
$reports = $conn->query("
    SELECT
        campaigns.campaign_name,
        campaigns.fundraising_goal,
        COALESCE(SUM(donations.amount),0) AS total_raised
    FROM campaigns
    LEFT JOIN donations
        ON campaigns.campaign_id = donations.campaign_id
    GROUP BY campaigns.campaign_id
    ORDER BY total_raised DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Financial Reports</title>
<link rel="stylesheet" href="style.css">

<style>

/* KPI GRID */
.kpi-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
    margin-bottom:20px;
}

.kpi-card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    text-align:center;
}

.kpi-card h2{
    margin:0;
    color:#3498db;
}

/* SECTION */
.section{
    margin-top:20px;
}

/* PROGRESS BAR */
.progress-bar{
    width:100%;
    background:#e0e0e0;
    height:18px;
    border-radius:20px;
    overflow:hidden;
}

.progress-fill{
    height:100%;
    background:#27ae60;
    color:white;
    font-size:12px;
    text-align:center;
    line-height:18px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

table th, table td{
    border:1px solid #ddd;
    padding:10px;
    text-align:left;
}

table th{
    background:#f4f6f9;
}

/* INSIGHT BOX */
.insight{
    background:#eef6ff;
    padding:15px;
    border-radius:10px;
    margin-bottom:15px;
    font-size:14px;
    color:#2c3e50;
}

</style>
</head>

<body>

<header>
    <h1>Financial Reports Dashboard</h1>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<!-- INSIGHT -->
<div class="insight">
    📊 This report shows donation performance across all campaigns, helping track fundraising efficiency and financial impact.
</div>

<!-- KPI SECTION -->
<div class="kpi-grid">

    <div class="kpi-card">
        <h4>Total Donors</h4>
        <h2><?= $totalDonors ?></h2>
    </div>

    <div class="kpi-card">
        <h4>Total Donations</h4>
        <h2><?= $totalDonations ?></h2>
    </div>

    <div class="kpi-card">
        <h4>Total Raised</h4>
        <h2>R<?= number_format($totalRaised,2) ?></h2>
    </div>

    <div class="kpi-card">
        <h4>Campaigns</h4>
        <h2><?= $totalCampaigns ?></h2>
    </div>

</div>

<!-- CAMPAIGN REPORT -->
<div class="card section">

<h2>Campaign Performance Report</h2>

<table>

<thead>
<tr>
    <th>Campaign</th>
    <th>Goal</th>
    <th>Raised</th>
    <th>Progress</th>
</tr>
</thead>

<tbody>

<?php foreach ($reports as $r): ?>

<?php
$goal = max(1, (float)$r['fundraising_goal']);
$raised = (float)$r['total_raised'];
$percent = min(100, round(($raised / $goal) * 100));
?>

<tr>
    <td><?= htmlspecialchars($r['campaign_name']) ?></td>
    <td>R<?= number_format($r['fundraising_goal'],2) ?></td>
    <td>R<?= number_format($raised,2) ?></td>
    <td>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $percent ?>%;">
                <?= $percent ?>%
            </div>
        </div>
    </td>
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