<?php
require_once '../../config/db.php';
$page_title = 'Exams';
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM exams WHERE id=$id");
    $_SESSION['success'] = "Exam deleted!";
    header("Location: index.php"); exit();
}
$exams = $conn->query("SELECT e.*, COUNT(DISTINCT eb.batch_id) as batch_count, COUNT(DISTINCT sp.id) as seat_count
    FROM exams e 
    LEFT JOIN exam_batches eb ON e.id=eb.exam_id
    LEFT JOIN seat_plan sp ON e.id=sp.exam_id
    GROUP BY e.id ORDER BY e.exam_date DESC")->fetch_all(MYSQLI_ASSOC);
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Exams</h2><p>Schedule and manage university examinations</p></div>
    <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Schedule Exam</a>
</div>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<div class="card">
    <div class="card-header"><h4><i class="fa fa-calendar-days"></i> All Exams (<?= count($exams) ?>)</h4></div>
    <div class="table-wrapper">
        <?php if (empty($exams)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h4>No Exams Scheduled</h4>
                <a href="add.php" class="btn btn-primary">Schedule First Exam</a>
            </div>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Exam Title</th><th>Date</th><th>Time</th><th>Batches</th><th>Seats</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($exams as $i => $e): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
                <td><?= date('d M Y', strtotime($e['exam_date'])) ?></td>
                <td><?= date('h:i A', strtotime($e['start_time'])) ?> - <?= date('h:i A', strtotime($e['end_time'])) ?></td>
                <td><span class="badge badge-primary"><?= $e['batch_count'] ?></span></td>
                <td><span class="badge badge-success"><?= $e['seat_count'] ?></span></td>
                <td>
                    <?php if ($e['status']=='published'): ?><span class="badge badge-success">Published</span>
                    <?php elseif ($e['status']=='generated'): ?><span class="badge badge-warning">Generated</span>
                    <?php else: ?><span class="badge badge-gray">Pending</span><?php endif; ?>
                </td>
                <td style="display:flex;gap:5px;flex-wrap:wrap">
                    <a href="../../modules/seat_plan/generate.php?exam_id=<?= $e['id'] ?>" class="btn btn-primary btn-sm"><i class="fa fa-wand-magic-sparkles"></i> Generate</a>
                    <a href="../../modules/seat_plan/view.php?exam_id=<?= $e['id'] ?>" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                    <a href="edit.php?id=<?= $e['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i></a>
                    <button onclick="confirmDelete('index.php?delete=<?= $e['id'] ?>', '<?= addslashes($e['title']) ?>')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
