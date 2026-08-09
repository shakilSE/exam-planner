<?php
require_once '../../config/db.php';
$page_title = 'Teachers';
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM teachers WHERE id=$id");
    $_SESSION['success'] = "Teacher deleted!";
    header("Location: index.php"); exit();
}
$teachers = $conn->query("SELECT * FROM teachers ORDER BY duty_count ASC")->fetch_all(MYSQLI_ASSOC);
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Teachers / Invigilators</h2><p>Manage invigilators for exam duties</p></div>
    <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Teacher</a>
</div>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<div class="card">
    <div class="card-header"><h4><i class="fa fa-chalkboard-teacher"></i> All Teachers (<?= count($teachers) ?>)</h4></div>
    <div class="table-wrapper">
        <?php if (empty($teachers)): ?>
            <div class="empty-state">
                <div class="empty-icon">👨‍🏫</div>
                <h4>No Teachers Yet</h4>
                <a href="add.php" class="btn btn-primary">Add First Teacher</a>
            </div>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Department</th><th>Email</th><th>Phone</th><th>Duty Count</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($teachers as $i => $t): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($t['name']) ?></strong></td>
                <td><?= htmlspecialchars($t['department'] ?? '-') ?></td>
                <td><?= htmlspecialchars($t['email'] ?? '-') ?></td>
                <td><?= htmlspecialchars($t['phone'] ?? '-') ?></td>
                <td>
                    <span class="badge <?= $t['duty_count'] > 3 ? 'badge-warning' : 'badge-success' ?>">
                        <?= $t['duty_count'] ?> duties
                    </span>
                </td>
                <td>
                    <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i></a>
                    <button onclick="confirmDelete('index.php?delete=<?= $t['id'] ?>', '<?= addslashes($t['name']) ?>')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
