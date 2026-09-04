<?php
require_once 'db.php';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $admin_invite_code = trim($_POST['admin_invite_code'] ?? '');

    // FIX: phone is now collected as a country code + local number pair
    // and combined into one value, e.g. "+27 821234567".
    $phone_code = trim($_POST['phone_code'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $phone = trim($phone_code . ' ' . $phone_number);
    $address = trim($_POST['address'] ?? '');

    // Validate required fields
    if (!$full_name || !$email || !$password || !$confirm_password || !$role) {
        $error = 'Please fill in all required fields.';
    }
    elseif ($password !== $confirm_password) {
        // NOTE: this server-side check is now a fallback - the form also
        // reports a mismatch immediately as the user types (see JS below).
        $error = 'Passwords do not match.';
    }
    // FIX: anyone could previously self-register as "Administrator" with no
    // restriction, meaning anyone could then log in as an admin. Registering
    // as Administrator now requires a valid invite code.
    elseif ($role === 'Administrator' && $admin_invite_code !== ADMIN_INVITE_CODE) {
        $error = 'A valid admin invite code is required to register as Administrator.';
    }
    else {
        try {

            // 🔍 Check if email already exists
            $check = $conn->prepare("SELECT 1 FROM users WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                $error = 'This email is already registered.';
            } else {

                // 🔄 Start transaction
                $conn->beginTransaction();

                // 🔐 Hash password
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // 👤 Insert into users table (IMPORTANT: uses users_id internally)
                $stmt = $conn->prepare('
                    INSERT INTO users (full_name, email, password_hash, role)
                    VALUES (?, ?, ?, ?)
                ');
                $stmt->execute([$full_name, $email, $hash, $role]);

                // Get inserted user ID (this is users_id in your DB)
                $users_id = $conn->lastInsertId();

                // 👥 If donor, insert into donors table (now linked via user_id)
                if ($role === 'Donor') {
                    $stmt = $conn->prepare('
                        INSERT INTO donors (user_id, name, email, phone, address)
                        VALUES (?, ?, ?, ?, ?)
                    ');
                    $stmt->execute([$users_id, $full_name, $email, $phone, $address]);
                }

                // ✅ Commit transaction
                $conn->commit();

                $msg = $role . ' account created successfully. You can now log in.';
            }

        } catch (PDOException $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            if ($e->getCode() == 23000) {
                $error = "This email is already registered.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-background">
  <div class="login-card">

    <div class="logo">
      <h1>SAVE THE CHILDREN SOUTH AFRICA NPO</h1>
      <p>Create your account</p>
    </div>

    <?php if ($msg): ?>
        <p class="success"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" id="registerForm">

      <label>Register As</label>
      <select name="role" id="roleSelect" required>
        <option value="">Select User Type</option>
        <option value="Administrator">Administrator</option>
        <option value="Staff Member">Staff Member</option>
        <option value="Donor">Donor</option>
      </select>

      <!-- FIX: Administrator sign-ups now require an invite code so that
           not anyone can register (and then log in) as an admin. -->
      <div id="adminInviteGroup" style="display:none;">
        <label>Admin Invite Code</label>
        <input type="text" name="admin_invite_code" id="adminInviteCode" autocomplete="off">
      </div>

      <label>Full Name</label>
      <input type="text" name="full_name" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" id="password" required>

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" id="confirm_password" required>

      <!-- FIX: reports a mismatch immediately as the user types, instead of
           only after the form is submitted. -->
      <p id="passwordMatchMsg" style="display:none;color:#c0392b;font-size:13px;margin-top:-6px;"></p>

      <!-- FIX: phone number now includes a country code -->
      <label>Phone (Donor optional)</label>
      <div style="display:flex;gap:8px;">
        <select name="phone_code" id="phoneCode" style="width:130px;flex:none;">
          <option value="+27">🇿🇦 +27 (SA)</option>
          <option value="+264">🇳🇦 +264 (NA)</option>
          <option value="+267">🇧🇼 +267 (BW)</option>
          <option value="+258">🇲🇿 +258 (MZ)</option>
          <option value="+263">🇿🇼 +263 (ZW)</option>
          <option value="+44">🇬🇧 +44 (UK)</option>
          <option value="+1">🇺🇸 +1 (US)</option>
          <option value="other">Other…</option>
        </select>
        <input type="text" name="phone_number" id="phoneNumber" placeholder="e.g. 821234567" style="flex:1;">
      </div>

      <label>Address (Donor optional)</label>
      <input type="text" name="address">

      <button type="submit" id="submitBtn">Create Account</button>

      <p class="register-text">
        Already have an account? <a href="index.php">Login</a>
      </p>

    </form>

    <script>
      // FIX: show/require the admin invite code only when "Administrator" is selected
      const roleSelect = document.getElementById('roleSelect');
      const adminGroup = document.getElementById('adminInviteGroup');
      const adminInput = document.getElementById('adminInviteCode');

      function toggleAdminField() {
        const isAdmin = roleSelect.value === 'Administrator';
        adminGroup.style.display = isAdmin ? 'block' : 'none';
        adminInput.required = isAdmin;
      }
      roleSelect.addEventListener('change', toggleAdminField);
      toggleAdminField();

      // FIX: report a password mismatch immediately, instead of waiting for submit
      const pw = document.getElementById('password');
      const confirmPw = document.getElementById('confirm_password');
      const msg = document.getElementById('passwordMatchMsg');
      const submitBtn = document.getElementById('submitBtn');

      function checkPasswordsMatch() {
        if (confirmPw.value.length === 0) {
          msg.style.display = 'none';
          return true;
        }
        if (pw.value !== confirmPw.value) {
          msg.textContent = 'Passwords do not match.';
          msg.style.display = 'block';
          return false;
        }
        msg.style.display = 'none';
        return true;
      }
      pw.addEventListener('input', checkPasswordsMatch);
      confirmPw.addEventListener('input', checkPasswordsMatch);

      document.getElementById('registerForm').addEventListener('submit', function (e) {
        if (!checkPasswordsMatch()) {
          e.preventDefault();
        }
      });
    </script>

  </div>
</div>

<footer>
  <p>© 2026 PTY Ltd. All Rights Reserved.</p>
</footer>

</body>
</html>