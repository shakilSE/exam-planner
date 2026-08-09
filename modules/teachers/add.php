<?php
require_once '../../config/db.php';
$page_title = 'Add Teacher';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $dept = sanitize($conn, $_POST['department']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    if ($name) {
        $conn->query("INSERT INTO teachers (name,department,email,phone) VALUES ('$name','$dept','$email','$phone')");
        $_SESSION['success'] = "Teacher added!";
        header("Location: index.php"); exit();
    } else { $error = "Name is required."; }
}
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Add Teacher</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<div class="card" style="max-width:500px">
    <div class="card-header"><h4><i class="fa fa-plus"></i> Teacher Details</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group"><label>Full Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Rahman" required></div>
            <div class="form-group"><label>Department</label>
                <input type="text" name="department" class="form-control" placeholder="e.g. Computer Science"></div>
            <div class="form-group"><label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="teacher@uni.edu"></div>
            <div class="form-group"><label>Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="01XXXXXXXXX"></div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save Teacher</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
