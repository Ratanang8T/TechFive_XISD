<?php
require_once 'auth.php'; require_login(); require_once 'db.php';
$msg=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $full_name=trim($_POST['full_name']); $email=trim($_POST['email']);
    $current=$_POST['current_password'] ?? ''; $new=$_POST['new_password'] ?? ''; $confirm=$_POST['confirm_password'] ?? '';
    $stmt=$conn->prepare('SELECT * FROM users WHERE user_id=?'); $stmt->execute([$_SESSION['user_id']]); $user=$stmt->fetch(PDO::FETCH_ASSOC);
    if(!$full_name || !$email){ $error='Name and email are required.'; }
    elseif($new || $current || $confirm){
        if(!$current || !$new || !$confirm){ $error='Complete all password fields.'; }
        elseif(!password_verify($current,$user['password_hash'])){ $error='Current password is incorrect.'; }
        elseif($new!==$confirm){ $error='New passwords do not match.'; }
        else{
            $hash=password_hash($new,PASSWORD_DEFAULT);
            $stmt=$conn->prepare('UPDATE users SET full_name=?, email=?, password_hash=? WHERE user_id=?'); $stmt->execute([$full_name,$email,$hash,$_SESSION['user_id']]);
            $_SESSION['full_name']=$full_name; $_SESSION['email']=$email; $msg='Profile and password updated.';
        }
    } else {
        $stmt=$conn->prepare('UPDATE users SET full_name=?, email=? WHERE user_id=?'); $stmt->execute([$full_name,$email,$_SESSION['user_id']]);
        $_SESSION['full_name']=$full_name; $_SESSION['email']=$email; $msg='Profile updated.';
    }
}
?>
<!DOCTYPE html><html><head><title>Settings</title><link rel="stylesheet" href="style.css"></head><body>
<header><h1>Settings</h1></header><?php include 'nav.php'; ?>
<div class="container"><div class="card"><h2>Account Details</h2>
<?php if($msg): ?><p class="success"><?= htmlspecialchars($msg) ?></p><?php endif; ?><?php if($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="POST" id="profileForm"><label>Full Name</label><input type="text" name="full_name" value="<?= htmlspecialchars($_SESSION['full_name']) ?>" required><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" required><label>Role</label><input type="text" value="<?= htmlspecialchars($_SESSION['role']) ?>" disabled><h3>Change Password</h3><label>Current Password</label><input type="password" name="current_password" id="current_password"><label>New Password</label><input type="password" name="new_password" id="new_password"><label>Confirm New Password</label><input type="password" name="confirm_password" id="confirm_password_settings">
<!-- FIX: reports a mismatch immediately as the user types, same as Register -->
<p id="settingsPasswordMsg" style="display:none;color:#c0392b;font-size:13px;margin-top:-6px;"></p>
<button type="submit">Save Changes</button></form>
<script>
(function(){
  const newPw = document.getElementById('new_password');
  const confirmPw = document.getElementById('confirm_password_settings');
  const msg = document.getElementById('settingsPasswordMsg');
  function check(){
    if (confirmPw.value.length === 0) { msg.style.display='none'; return true; }
    if (newPw.value !== confirmPw.value) { msg.textContent='Passwords do not match.'; msg.style.display='block'; return false; }
    msg.style.display='none'; return true;
  }
  newPw.addEventListener('input', check);
  confirmPw.addEventListener('input', check);
  document.getElementById('profileForm').addEventListener('submit', function(e){ if(!check() && confirmPw.value.length>0){ e.preventDefault(); } });
})();
</script>
</div></div><footer><p>© 2026 Save the Children South Africa. All Rights Reserved.</p></footer></body></html>
