<?php
require_once '../../config/db.php';
$page_title = 'Edit Student';
$id = (int)($_GET['id'] ?? 0);
$s = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
if (!$s) { header("Location: index.php"); exit(); }
$batches = $conn->query("SELECT * FROM batches ORDER BY name")->fetch_all(MYSQLI_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll = sanitize($conn, $_POST['roll']);
    $name = sanitize($conn, $_POST['name']);
    $batch_id = (int)$_POST['batch_id'];
    $email = sanitize($conn, $_POST['email']);
    $conn->query("UPDATE students SET roll='$roll',name='$name',batch_id=$batch_id,email='$email' WHERE id=$id");
    $_SESSION['success'] = "Student updated!";
    header("Location: index.php"); exit();
}
include '../../includes/header.php';
?>
<div class="page-title"><div><h2>Edit Student</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a></div>
<div class="card" style="max-width:500px">
    <div class="card-header"><h4><i class="fa fa-pen"></i> Edit Student</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group"><label>Roll Number *</label>
                <input type="text" name="roll" class="form-control" value="<?= htmlspecialchars($s['roll']) ?>" required></div>
            <div class="form-group"><label>Full Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($s['name']) ?>" required></div>
            <div class="form-group"><label>Batch *</label>
                <select name="batch_id" class="form-control" required>
                    <?php foreach ($batches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $s['batch_id'] == $b['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($s['email'] ?? '') ?>"></div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Update Student</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
