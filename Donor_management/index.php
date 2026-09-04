<?php
session_start();
require_once 'db.php';

// If already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Get user by email
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validate user
    if ($user && $user['role'] === $role && password_verify($password, $user['password_hash'])) {

        // ✅ FIXED: correct primary key = users_id
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        header('Location: dashboard.php');
        exit();

    } else {
        $error = 'Invalid email, password, or user type.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NPO Donor Management System</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="login-background">
  <div class="login-card">

    <div class="logo">
      <h1>SAVE THE CHILDREN SOUTH AFRICA NPO</h1>
      <p>Donor Management System</p>
    </div>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">

      <label>Login As</label>
      <select name="role" required>
        <option value="">Select User Type</option>
        <option value="Administrator">Administrator</option>
        <option value="Staff Member">Staff Member</option>
        <option value="Donor">Donor</option>
      </select>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>

      <p class="register-text">
        First time here? <a href="register.php">Create account</a>
      </p>

    </form>

  </div>
</div>

<footer>
  <p>© 2026 PTY Ltd. All Rights Reserved.</p>
</footer>

</body>
</html>