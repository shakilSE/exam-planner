<?php
require_once '../../config/db.php';
$page_title = 'Edit Teacher';
$id = (int)($_GET['id'] ?? 0);
$t = $conn->query("SELECT * FROM teachers WHERE id=$id")->fetch_assoc();
if (!$t) { header("Location: index.php"); exit(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $dept = sanitize($conn, $_POST['department']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $conn->query("UPDATE teachers SET name='$name',department='$dept',email='$email',phone='$phone' WHERE id=$id");
    $_SESSION['success'] = "Teacher updated!";
    header("Location: index.php"); exit();
}
include '../../includes/header.php';
?>
<div class="page-title"><div><h2>Edit Teacher</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a></div>
<div class="card" style="max-width:500px">
    <div class="card-header"><h4><i class="fa fa-pen"></i> Edit Teacher</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group"><label>Full Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($t['name']) ?>" required></div>
            <div class="form-group"><label>Department</label>
                <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($t['department'] ?? '') ?>"></div>
            <div class="form-group"><label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($t['email'] ?? '') ?>"></div>
            <div class="form-group"><label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($t['phone'] ?? '') ?>"></div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Update Teacher</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
