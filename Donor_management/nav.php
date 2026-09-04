<?php require_once 'auth.php'; ?>

<nav>
    <a href="home.php">Home</a>
    <a href="dashboard.php">Dashboard</a>

    <?php if (role_is('Administrator')): ?>
        <a href="donors.php">Donors</a>
        <a href="cash_pending.php">Cash Approvals</a>
    <?php endif; ?>

    <?php if (role_is('Administrator') || role_is('Donor')): ?>
        <a href="donations.php">Donations</a>
    <?php endif; ?>

    <a href="campaigns.php">Campaigns</a>

    <?php if (role_is('Administrator') || role_is('Staff Member')): ?>
        <a href="messages.php">Messages</a>
        <a href="reports.php">Reports</a>
    <?php endif; ?>

    <a href="profile.php">Settings</a>
    <a href="logout.php">Logout</a>
</nav>