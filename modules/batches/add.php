<?php
require_once '../../config/db.php';
$page_title = 'Add Batch';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $dept = sanitize($conn, $_POST['department']);
    if ($name && $dept) {
        $conn->query("INSERT INTO batches (name, department) VALUES ('$name', '$dept')");
        $_SESSION['success'] = "Batch added successfully!";
        header("Location: index.php"); exit();
    } else { $error = "Please fill all fields.";
     }
}
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Add Batch</h2><p>Create a new batch or department group</p></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<div class="card" style="max-width:500px">
    <div class="card-header"><h4><i class="fa fa-plus"></i> Batch Information</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Batch Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. CSE-A 2021" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Department *</label>
                <input type="text" name="department" class="form-control" placeholder="e.g. Computer Science" required value="<?= htmlspecialchars($_POST['department'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save Batch</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
