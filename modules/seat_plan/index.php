<?php
require_once '../../config/db.php';
$page_title = 'Seat Plans';
$exams = $conn->query("SELECT e.*, COUNT(DISTINCT sp.id) as seat_count, COUNT(DISTINCT sp.room_id) as room_count
    FROM exams e LEFT JOIN seat_plan sp ON e.id=sp.exam_id 
    GROUP BY e.id ORDER BY e.exam_date DESC")->fetch_all(MYSQLI_ASSOC);
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Seat Plans</h2><p>View and manage all generated seat plans</p></div>
    <a href="../exams/add.php" class="btn btn-primary"><i class="fa fa-plus"></i> New Exam</a>
</div>
<div class="card">
    <div class="card-header"><h4><i class="fa fa-table-cells"></i> All Seat Plans</h4></div>
    <div class="table-wrapper">
        <?php if (empty($exams)): ?>
            <div class="empty-state"><div class="empty-icon">📋</div>
                <h4>No Exams Yet</h4>
                <a href="../exams/add.php" class="btn btn-primary">Schedule Exam</a>
            </div>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Exam</th><th>Date</th><th>Students Seated</th><th>Rooms Used</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($exams as $i => $e): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
                <td><?= date('d M Y', strtotime($e['exam_date'])) ?></td>
                <td><span class="badge badge-success"><?= $e['seat_count'] ?></span></td>
                <td><span class="badge badge-primary"><?= $e['room_count'] ?></span></td>
                <td>
                    <?php if ($e['status']=='published'): ?><span class="badge badge-success">Published</span>
                    <?php elseif ($e['status']=='generated'): ?><span class="badge badge-warning">Generated</span>
                    <?php else: ?><span class="badge badge-gray">Pending</span><?php endif; ?>
                </td>
                <td style="display:flex;gap:6px">
                    <?php if ($e['seat_count'] > 0): ?>
                        <a href="view.php?exam_id=<?= $e['id'] ?>" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a>
                        <a href="print.php?exam_id=<?= $e['id'] ?>" class="btn btn-warning btn-sm" target="_blank"><i class="fa fa-print"></i> Print</a>
                    <?php endif; ?>
                    <a href="generate.php?exam_id=<?= $e['id'] ?>" class="btn btn-success btn-sm"><i class="fa fa-wand-magic-sparkles"></i> Generate</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
