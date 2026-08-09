<?php
require_once '../../config/db.php';
$page_title = 'Batches';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM batches WHERE id=$id");
    $_SESSION['success'] = "Batch deleted successfully!";
    header("Location: index.php"); exit();
}

$batches = $conn->query("SELECT b.*, COUNT(s.id) as student_count 
    FROM batches b LEFT JOIN students s ON b.id = s.batch_id 
    GROUP BY b.id ORDER BY b.created_at DESC")->fetch_all(MYSQLI_ASSOC);

include '../../includes/header.php';
?>

<div class="page-title">
    <div>
        <h2>Batches</h2>
        <p>Manage all university batches and departments</p>
    </div>
    <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Batch</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h4><i class="fa-solid fa-users"></i> All Batches (<?= count($batches) ?>)</h4>
    </div>
    <div class="table-wrapper">
        <?php if (empty($batches)): ?>
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <h4>No Batches Yet</h4>
                <p>Add batches to get started</p>
                <a href="add.php" class="btn btn-primary">Add First Batch</a>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>#</th><th>Batch Name</th><th>Department</th><th>Students</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($batches as $i => $b): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($b['name']) ?></strong></td>
                <td><span class="badge badge-primary"><?= htmlspecialchars($b['department']) ?></span></td>
                <td><span class="badge badge-success"><?= $b['student_count'] ?> students</span></td>
                <td>
                    <a href="edit.php?id=<?= $b['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Edit</a>
                    <button onclick="confirmDelete('index.php?delete=<?= $b['id'] ?>', '<?= addslashes($b['name']) ?>')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
