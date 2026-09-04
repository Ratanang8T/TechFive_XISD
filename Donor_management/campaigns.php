<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

$message = '';
$canManage = role_is('Administrator') || role_is('Staff Member');

/* =========================
   DELETE CAMPAIGN (NEW)
========================= */
if (isset($_POST['delete_campaign']) && $canManage) {

    $stmt = $conn->prepare("DELETE FROM campaigns WHERE campaign_id = ?");
    $stmt->execute([$_POST['campaign_id']]);

    $message = "Campaign deleted successfully.";
}

/* =========================
   CREATE CAMPAIGN
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canManage && isset($_POST['campaign_name'])) {

    $stmt = $conn->prepare(
        'INSERT INTO campaigns (campaign_name, fundraising_goal, created_at)
         VALUES (?, ?, NOW())'
    );

    $stmt->execute([
        trim($_POST['campaign_name']),
        $_POST['fundraising_goal']
    ]);

    $message = 'Campaign created successfully.';
}

/* =========================
   FETCH CAMPAIGNS
========================= */
$campaigns = $conn->query("
    SELECT
        campaigns.*,
        COALESCE(SUM(donations.amount),0) AS raised
    FROM campaigns
    LEFT JOIN donations
        ON campaigns.campaign_id = donations.campaign_id
    GROUP BY campaigns.campaign_id
    ORDER BY campaigns.campaign_id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Campaigns</title>
    <link rel="stylesheet" href="style.css">

    <style>

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .campaign-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }

        .progress-bar {
            width: 100%;
            background: #e0e0e0;
            height: 18px;
            border-radius: 20px;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #27ae60;
            color: white;
            font-size: 12px;
            text-align: center;
            line-height: 18px;
        }

        .delete-btn {
            margin-top: 10px;
            background: #e74c3c;
            border: none;
            color: white;
            padding: 8px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

    </style>
</head>

<body>

<header>
    <h1>Fundraising Campaigns</h1>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<?php if ($message): ?>
    <p class="success"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($canManage): ?>
<div class="card">

    <h2>Create Campaign</h2>

    <form method="POST">

        <label>Campaign Name</label>
        <input type="text" name="campaign_name" required>

        <label>Fundraising Goal</label>
        <input type="number" step="0.01" name="fundraising_goal" required>

        <button type="submit">Create Campaign</button>

    </form>

</div>
<?php endif; ?>

<!-- CAMPAIGN GRID -->
<div class="card">

<h2>Campaign Progress</h2>

<div class="grid">

<?php foreach ($campaigns as $c): ?>

<?php
$goal = max(1, (float)$c['fundraising_goal']);
$raised = (float)$c['raised'];
$percent = min(100, round(($raised / $goal) * 100));
?>

<div class="campaign-card">

    <div class="title">
        <?= htmlspecialchars($c['campaign_name']) ?>
    </div>

    <div class="meta">
        <span>Raised: R<?= number_format($raised,2) ?></span>
        <span>Goal: R<?= number_format($goal,2) ?></span>
    </div>

    <div class="progress-bar">
        <div class="progress-fill" style="width: <?= $percent ?>%;">
            <?= $percent ?>%
        </div>
    </div>

    <small>Created: <?= htmlspecialchars($c['created_at']) ?></small>

    <!-- DELETE BUTTON (ADMIN ONLY) -->
    <?php if ($canManage): ?>
        <form method="POST" onsubmit="return confirm('Delete this campaign? This cannot be undone!');">
            <input type="hidden" name="campaign_id" value="<?= $c['campaign_id'] ?>">
            <button class="delete-btn" type="submit" name="delete_campaign">
                Delete Campaign
            </button>
        </form>
    <?php endif; ?>

</div>

<?php endforeach; ?>

</div>

</div>

<footer>
    <p>© 2026 Save the Children South Africa. All Rights Reserved.</p>
</footer>

</body>
</html>