<?php
require_once '../../config/db.php';
$page_title = 'Students';
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$id");
    $_SESSION['success'] = "Student deleted!";
    header("Location: index.php"); exit();
}
$batch_filter = (int)($_GET['batch'] ?? 0);
$where = $batch_filter ? "WHERE s.batch_id=$batch_filter" : "";
$students = $conn->query("SELECT s.*, b.name as batch_name FROM students s 
    JOIN batches b ON s.batch_id=b.id $where ORDER BY s.roll ASC")->fetch_all(MYSQLI_ASSOC);
$batches = $conn->query("SELECT * FROM batches ORDER BY name")->fetch_all(MYSQLI_ASSOC);
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Students</h2><p>Manage all registered students</p></div>
    <div style="display:flex;gap:10px;align-items:center">
        <form method="GET" style="display:flex;gap:8px;align-items:center">
            <select name="batch" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="">All Batches</option>
                <?php foreach ($batches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= $batch_filter == $b['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Student</a>
        <a href="import.php" class="btn btn-success"><i class="fa fa-file-import"></i> Import CSV</a>
    </div>
</div>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<div class="card">
    <div class="card-header"><h4><i class="fa fa-user-graduate"></i> Students (<?= count($students) ?>)</h4></div>
    <div class="table-wrapper">
        <?php if (empty($students)): ?>
            <div class="empty-state">
                <div class="empty-icon">🎓</div>
                <h4>No Students Yet</h4>
                <a href="add.php" class="btn btn-primary">Add Student</a>
            </div>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Roll</th><th>Name</th><th>Batch</th><th>Email</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($students as $i => $s): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($s['roll']) ?></strong></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><span class="badge badge-primary"><?= htmlspecialchars($s['batch_name']) ?></span></td>
                <td><?= htmlspecialchars($s['email'] ?? '-') ?></td>
                <td>
                    <a href="edit.php?id=<?= $s['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i></a>
                    <button onclick="confirmDelete('index.php?delete=<?= $s['id'] ?>', '<?= addslashes($s['name']) ?>')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
