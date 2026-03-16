<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';


require_login();

if (is_admin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = intval($_GET['id'] ?? 0);



$stmt = mysqli_prepare($conn,
    "SELECT * FROM relief_requests WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$req = mysqli_fetch_assoc($result);


if (!$req) {
    header("Location: requests.php");
    exit();
}



$districts = [
    'Colombo','Gampaha','Kalutara','Kandy','Matale','Nuwara Eliya',
    'Galle','Matara','Hambantota','Jaffna','Kilinochchi','Mannar',
    'Vavuniya','Mullaitivu','Batticaloa','Ampara','Trincomalee',
    'Kurunegala','Puttalam','Anuradhapura','Polonnaruwa',
    'Badulla','Moneragala','Ratnapura','Kegalle'
];

$error = "";



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $relief_type  = $_POST['relief_type'];
    $district     = $_POST['district'];
    $div_sec      = trim($_POST['divisional_secretariat']);
    $gn_div       = trim($_POST['gn_division']);
    $contact_name = trim($_POST['contact_person']);
    $contact_num  = trim($_POST['contact_number']);
    $address      = trim($_POST['address']);
    $family       = intval($_POST['family_members']);
    $severity     = $_POST['severity'];
    $description  = trim($_POST['description']);

    
    if (
        empty($district) ||
        empty($div_sec) ||
        empty($gn_div) ||
        empty($contact_name) ||
        empty($contact_num) ||
        empty($address) ||
        $family < 1
    ) {
        $error = "Please fill in all required fields.";
    } 
    else {

        $update_sql = "
            UPDATE relief_requests 
            SET relief_type=?, district=?, divisional_secretariat=?, gn_division=?,
                contact_person=?, contact_number=?, address=?, family_members=?,
                severity=?, description=?
            WHERE id=? AND user_id=?
        ";

        $upd = mysqli_prepare($conn, $update_sql);

        mysqli_stmt_bind_param(
            $upd,
            "sssssssissii",
            $relief_type,
            $district,
            $div_sec,
            $gn_div,
            $contact_name,
            $contact_num,
            $address,
            $family,
            $severity,
            $description,
            $id,
            $user_id
        );

        if (mysqli_stmt_execute($upd)) {

            header("Location: requests.php?updated=1");
            exit();

        } else {

            $error = "Update failed. Please try again.";

        }
    }

    
    $req = array_merge($req, $_POST);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Request — Flood Relief</title>
<link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="app-wrapper">

<?php include 'sidebar.php'; ?>
<main class="main-content">


<div class="page-header">
    <h1>Edit Request #<?= $id ?></h1>
    
</div>


<?php if ($error): ?>
<div class="alert alert-error">
    ⚠️ <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>


<div class="card">

<div class="card-body">

<form method="POST">


<div class="section-divider">📍 Location Details</div>

<div class="form-row">

<div class="form-group">
<label>District *</label>

<select name="district" required>
<option value="">Select District</option>

<?php foreach ($districts as $d): ?>
<option value="<?= $d ?>" <?= ($req['district'] === $d) ? 'selected' : '' ?>>
<?= $d ?>
</option>
<?php endforeach; ?>

</select>
</div>

<div class="form-group">
<label>Divisional Secretariat *</label>

<input type="text"
name="divisional_secretariat"
value="<?= htmlspecialchars($req['divisional_secretariat']) ?>"
required>

</div>

</div>


<div class="form-group">

<label>GN Division *</label>

<input type="text"
name="gn_division"
value="<?= htmlspecialchars($req['gn_division']) ?>"
required>

</div>



<div class="section-divider">🏠 Household Details</div>

<div class="form-row">

<div class="form-group">
<label>Contact Person Name *</label>

<input type="text"
name="contact_person"
value="<?= htmlspecialchars($req['contact_person']) ?>"
required>

</div>

<div class="form-group">

<label>Contact Number *</label>

<input type="tel"
name="contact_number"
value="<?= htmlspecialchars($req['contact_number']) ?>"
required>

</div>

</div>


<div class="form-group">

<label>Address *</label>

<input type="text"
name="address"
value="<?= htmlspecialchars($req['address']) ?>"
required>

</div>


<div class="form-group">

<label>Number of Family Members *</label>

<input type="number"
name="family_members"
min="1"
max="50"
value="<?= htmlspecialchars($req['family_members']) ?>"
required>

</div>



<div class="section-divider">🆘 Relief Details</div>
<div class="form-row">
<div class="form-group">

<label>Type of Relief *</label>

<select name="relief_type" required>

<?php foreach (['Food','Water','Medicine','Shelter'] as $type): ?>

<option value="<?= $type ?>" <?= ($req['relief_type'] === $type) ? 'selected' : '' ?>>
<?= $type ?>
</option>

<?php endforeach; ?>

</select>

</div>


<div class="form-group">

<label>Flood Severity *</label>

<select name="severity" required>

<?php foreach (['Low','Medium','High'] as $sev): ?>

<option value="<?= $sev ?>" <?= ($req['severity'] === $sev) ? 'selected' : '' ?>>
<?= $sev ?>
</option>

<?php endforeach; ?>

</select>

</div>

</div>


<div class="form-group">

<label>Description / Special Requirements</label>

<textarea name="description">
<?= htmlspecialchars($req['description']) ?>
</textarea>

</div>


<div style="display:flex; gap:12px; margin-top:8px;">

<button type="submit"
class="btn btn-primary"
style="max-width:220px;">
Save Changes →
</button>

<a href="requests.php" class="btn btn-secondary">
Cancel
</a>

</div>
</form>
</div>
</div>
</main>
</div>

</body>
</html>