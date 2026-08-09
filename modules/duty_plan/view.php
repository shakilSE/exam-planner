<?php
require_once '../../config/db.php';
$page_title = 'Duty Plan';
$exam_id = (int)($_GET['exam_id'] ?? 0);
$exam = $conn->query("SELECT * FROM exams WHERE id=$exam_id")->fetch_assoc();
if (!$exam) { header("Location: index.php"); exit(); }

$duties = $conn->query("SELECT dp.*, t.name as teacher_name, t.department, t.phone,
    r.room_no, r.building, r.capacity,
    COUNT(sp.id) as seated_count
    FROM duty_plan dp
    JOIN teachers t ON dp.teacher_id=t.id
    JOIN rooms r ON dp.room_id=r.id
    LEFT JOIN seat_plan sp ON sp.room_id=dp.room_id AND sp.exam_id=dp.exam_id
    WHERE dp.exam_id=$exam_id
    GROUP BY dp.id
    ORDER BY r.room_no")->fetch_all(MYSQLI_ASSOC);

// Teacher duty summary
$teacher_summary = $conn->query("SELECT t.name, t.department, COUNT(dp.id) as total_duties
    FROM teachers t LEFT JOIN duty_plan dp ON t.id=dp.teacher_id
    GROUP BY t.id ORDER BY total_duties DESC")->fetch_all(MYSQLI_ASSOC);

include '../../includes/header.php';
?>
<div class="page-title">
    <div>
        <h2>Duty Plan — <?= htmlspecialchars($exam['title']) ?></h2>
        <p><?= date('d F Y', strtotime($exam['exam_date'])) ?> | <?= date('h:i A', strtotime($exam['start_time'])) ?> - <?= date('h:i A', strtotime($exam['end_time'])) ?></p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="../../modules/seat_plan/view.php?exam_id=<?= $exam_id ?>" class="btn btn-outline"><i class="fa fa-table-cells"></i> Seat Plan</a>
        <a href="../../modules/seat_plan/print.php?exam_id=<?= $exam_id ?>" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Print</a>
    </div>
</div>

<!-- Duty Assignments -->
<div class="card" style="margin-bottom:22px">
    <div class="card-header"><h4><i class="fa fa-clipboard-list" style="color:#4F46E5"></i> Room-wise Duty Assignment</h4></div>
    <div class="table-wrapper">
        <?php if (empty($duties)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h4>No duty plan generated yet</h4>
                <a href="../../modules/seat_plan/generate.php?exam_id=<?= $exam_id ?>" class="btn btn-primary">Generate Now</a>
            </div>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Room</th><th>Building</th><th>Invigilator</th><th>Department</th><th>Phone</th><th>Students</th></tr></thead>
            <tbody>
            <?php foreach ($duties as $i => $d): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong>Room <?= htmlspecialchars($d['room_no']) ?></strong></td>
                <td><?= htmlspecialchars($d['building'] ?? '-') ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:32px;height:32px;background:#4F46E5;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px">
                            <?= strtoupper(substr($d['teacher_name'], 0, 1)) ?>
                        </div>
                        <strong><?= htmlspecialchars($d['teacher_name']) ?></strong>
                    </div>
                </td>
                <td><?= htmlspecialchars($d['department'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['phone'] ?? '-') ?></td>
                <td><span class="badge badge-success"><?= $d['seated_count'] ?> students</span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Teacher Duty Count Summary -->
<div class="card">
    <div class="card-header"><h4><i class="fa fa-chart-bar" style="color:#10B981"></i> Overall Teacher Duty Summary</h4></div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Teacher</th><th>Department</th><th>Total Duties (All Exams)</th><th>Workload</th></tr></thead>
            <tbody>
            <?php foreach ($teacher_summary as $i => $ts): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($ts['name']) ?></strong></td>
                <td><?= htmlspecialchars($ts['department'] ?? '-') ?></td>
                <td><span class="badge <?= $ts['total_duties'] > 3 ? 'badge-warning' : 'badge-success' ?>"><?= $ts['total_duties'] ?> duties</span></td>
                <td>
                    <div style="background:#E2E8F0;border-radius:20px;height:8px;width:150px;overflow:hidden">
                        <?php $max_duty = max(array_column($teacher_summary, 'total_duties')) ?: 1; ?>
                        <div style="height:100%;width:<?= ($ts['total_duties']/$max_duty)*100 ?>%;background:<?= $ts['total_duties'] > 3 ? '#F59E0B' : '#10B981' ?>;border-radius:20px"></div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
