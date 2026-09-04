<?php
require_once 'auth.php';
require_admin();
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // FIX: phone now stored with its country code, e.g. "+27 821234567"
    $phone = trim(trim($_POST['phone_code'] ?? '') . ' ' . trim($_POST['phone_number'] ?? ''));

    $stmt = $conn->prepare('INSERT INTO donors (name, email, phone, address) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['name']),
        trim($_POST['email']),
        $phone,
        trim($_POST['address'])
    ]);

    $message = "Donor added successfully.";
}

$donors = $conn->query("SELECT * FROM donors ORDER BY donor_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Donors</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
<h1>Donor Management</h1>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<?php if ($message): ?>
<p class="success"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<div class="card">
<h2>Add Donor</h2>

<form method="POST">
<label>Name</label>
<input type="text" name="name" required>

<label>Email</label>
<input type="email" name="email" required>

<label>Phone</label>
<div style="display:flex;gap:8px;">
  <select name="phone_code" style="width:130px;flex:none;">
  <optgroup label="Country Codes">
      <option value="+27" selected>South Africa (+27)</option>
      <option value="+264">Namibia (+264)</option>
      <option value="+267">Botswana (+267)</option>
      <option value="+258">Mozambique (+258)</option>
      <option value="+263">Zimbabwe (+263)</option>
      <option value="+266">Lesotho (+266)</option>
      <option value="+268">Eswatini (+268)</option>
      <option value="+260">Zambia (+260)</option>
      <option value="+44">United Kingdom (+44)</option>
      <option value="+1">United States / Canada (+1)</option>
      <option value="+234">Nigeria (+234)</option>
      <option value="+91">India (+91)</option>
      <option value="+61">Australia (+61)</option>
      <option value="+49">Germany (+49)</option>
    </optgroup>
  
    <option value="other">Other (enter code manually)</option>
  </select>
  <input type="text" name="phone_number" placeholder="e.g. 821234567" required style="flex:1;">
</div>

<label>Address</label>
<input type="text" name="address" required>

<button type="submit">Add Donor</button>
</form>
</div>

<div class="card">
<h2>Donor List</h2>

<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Address</th>
</tr>

<?php foreach ($donors as $d): ?>
<tr>
<td><?= htmlspecialchars($d['name']) ?></td>
<td><?= htmlspecialchars($d['email']) ?></td>
<td><?= htmlspecialchars($d['phone']) ?></td>
<td><?= htmlspecialchars($d['address']) ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>

</div>

<footer>
<p>© 2026 Save the Children South Africa</p>
</footer>

</body>
</html>