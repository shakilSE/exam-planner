<?php
require_once '../../config/db.php';
$page_title = 'Duty Plans';
$exams = $conn->query("SELECT e.*, COUNT(DISTINCT dp.id) as duty_count 
    FROM exams e LEFT JOIN duty_plan dp ON e.id=dp.exam_id 
    GROUP BY e.id ORDER BY e.exam_date DESC")->fetch_all(MYSQLI_ASSOC);
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Invigilator Duty Plans</h2><p>View teacher duty assignments for each exam</p></div>
</div>
<div class="card">
    <div class="card-header"><h4><i class="fa fa-clipboard-list"></i> All Duty Plans</h4></div>
    <div class="table-wrapper">
        <?php if (empty($exams)): ?>
            <div class="empty-state"><div class="empty-icon">📋</div><h4>No exams yet</h4></div>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Exam</th><th>Date</th><th>Teachers Assigned</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($exams as $i => $e): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
                <td><?= date('d M Y', strtotime($e['exam_date'])) ?></td>
                <td><span class="badge badge-primary"><?= $e['duty_count'] ?> teachers</span></td>
                <td>
                    <?php if ($e['status']=='published'): ?><span class="badge badge-success">Published</span>
                    <?php elseif ($e['status']=='generated'): ?><span class="badge badge-warning">Generated</span>
                    <?php else: ?><span class="badge badge-gray">Pending</span><?php endif; ?>
                </td>
                <td>
                    <a href="view.php?exam_id=<?= $e['id'] ?>" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
