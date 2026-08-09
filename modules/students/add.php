<?php
require_once '../../config/db.php';
$page_title = 'Add Student';
$error = '';
$batches = $conn->query("SELECT * FROM batches ORDER BY name")->fetch_all(MYSQLI_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll = sanitize($conn, $_POST['roll']);
    $name = sanitize($conn, $_POST['name']);
    $batch_id = (int)$_POST['batch_id'];
    $email = sanitize($conn, $_POST['email']);
    if ($roll && $name && $batch_id) {
        $check = $conn->query("SELECT id FROM students WHERE roll='$roll'")->num_rows;
        if ($check > 0) { $error = "Roll number already exists!"; }
        else {
            $conn->query("INSERT INTO students (roll,name,batch_id,email) VALUES ('$roll','$name',$batch_id,'$email')");
            $_SESSION['success'] = "Student added!";
            header("Location: index.php"); exit();
        }
    } else { $error = "Please fill all required fields."; }
}
include '../../includes/header.php';
?>
<div class="page-title"><div><h2>Add Student</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a></div>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<div class="card" style="max-width:500px">
    <div class="card-header"><h4><i class="fa fa-plus"></i> Student Details</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group"><label>Roll Number *</label>
                <input type="text" name="roll" class="form-control" placeholder="e.g. CSE-2021-001" required></div>
            <div class="form-group"><label>Full Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Student's full name" required></div>
            <div class="form-group"><label>Batch *</label>
                <select name="batch_id" class="form-control" required>
                    <option value="">Select Batch</option>
                    <?php foreach ($batches as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="student@uni.edu"></div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save Student</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
