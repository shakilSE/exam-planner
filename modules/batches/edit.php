<?php
require_once '../../config/db.php';
$page_title = 'Edit Batch';
$id = (int)($_GET['id'] ?? 0);
$batch = $conn->query("SELECT * FROM batches WHERE id=$id")->fetch_assoc();
if (!$batch) { header("Location: index.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $dept = sanitize($conn, $_POST['department']);
    $conn->query("UPDATE batches SET name='$name', department='$dept' WHERE id=$id");
    $_SESSION['success'] = "Batch updated!";
    header("Location: index.php"); exit();
}
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Edit Batch</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:500px">
    <div class="card-header"><h4><i class="fa fa-pen"></i> Edit Batch</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Batch Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($batch['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Department *</label>
                <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($batch['department']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Update Batch</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
