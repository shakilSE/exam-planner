<?php
require_once '../../config/db.php';
$page_title = 'Rooms';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM rooms WHERE id=$id");
    $_SESSION['success'] = "Room deleted!";
    header("Location: index.php"); exit();
}

$rooms = $conn->query("SELECT * FROM rooms ORDER BY room_no ASC")->fetch_all(MYSQLI_ASSOC);
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Exam Rooms</h2><p>Manage exam halls and seating capacity</p></div>
    <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Room</a>
</div>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<div class="card">
    <div class="card-header"><h4><i class="fa fa-door-open"></i> All Rooms (<?= count($rooms) ?>)</h4></div>
    <div class="table-wrapper">
        <?php if (empty($rooms)): ?>
            <div class="empty-state">
                <div class="empty-icon">🚪</div>
                <h4>No Rooms Yet</h4>
                <a href="add.php" class="btn btn-primary">Add First Room</a>
            </div>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Room No</th><th>Building</th><th>Capacity</th><th>Layout (Row×Col)</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($rooms as $i => $r): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong>Room <?= htmlspecialchars($r['room_no']) ?></strong></td>
                <td><?= htmlspecialchars($r['building'] ?? '-') ?></td>
                <td><span class="badge badge-success"><?= $r['capacity'] ?> seats</span></td>
                <td><?= $r['rows'] ?> rows × <?= $r['cols'] ?> cols</td>
                <td>
                    <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Edit</a>
                    <button onclick="confirmDelete('index.php?delete=<?= $r['id'] ?>', 'Room <?= $r['room_no'] ?>')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
