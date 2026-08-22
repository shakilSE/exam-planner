<?php
require_once 'config/db.php';
$page_title = 'Dashboard';

$total_batches = $conn->query("SELECT COUNT(*) FROM batches")->fetch_row()[0];
$total_students = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$total_rooms = $conn->query("SELECT COUNT(*) FROM rooms")->fetch_row()[0];
$total_teachers = $conn->query("SELECT COUNT(*) FROM teachers")->fetch_row()[0];
$total_exams = $conn->query("SELECT COUNT(*) FROM exams")->fetch_row()[0];
$total_capacity = $conn->query("SELECT SUM(capacity) FROM rooms")->fetch_row()[0] ?? 0;

$upcoming_exams = $conn->query("SELECT e.*, COUNT(DISTINCT eb.batch_id) as batch_count 
    FROM exams e 
    LEFT JOIN exam_batches eb ON e.id = eb.exam_id 
    WHERE e.exam_date >= CURDATE() 
    GROUP BY e.id 
    ORDER BY e.exam_date ASC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$recent_exams = $conn->query("SELECT e.*, COUNT(DISTINCT sp.id) as seat_count 
    FROM exams e 
    LEFT JOIN seat_plan sp ON e.id = sp.exam_id 
    ORDER BY e.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-users" style="color:#4F46E5"></i></div>
        <div class="stat-info">
            <h3><?= $total_batches ?></h3>
            <p>Total Batches</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-user-graduate" style="color:#10B981"></i></div>
        <div class="stat-info">
            <h3><?= $total_students ?></h3>
            <p>Total Students</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fa-solid fa-door-open" style="color:#F59E0B"></i></div>
        <div class="stat-info">
            <h3><?= $total_rooms ?></h3>
            <p>Exam Rooms</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fa-solid fa-chalkboard-teacher" style="color:#7C3AED"></i></div>
        <div class="stat-info">
            <h3><?= $total_teachers ?></h3>
            <p>Invigilators</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-calendar-days" style="color:#EF4444"></i></div>
        <div class="stat-info">
            <h3><?= $total_exams ?></h3>
            <p>Total Exams</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-chair" style="color:#0EA5E9"></i></div>
        <div class="stat-info">
            <h3><?= $total_capacity ?></h3>
            <p>Total Capacity</p>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:22px; flex-wrap:wrap;">

<!-- Upcoming Exams -->
<div class="card">
    <div class="card-header">
        <h4><i class="fa-solid fa-calendar-check" style="color:#10B981"></i> Upcoming Exams</h4>
        <a href="<?= BASE_URL ?>modules/exams/index.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <div class="table-wrapper">
        <?php if (empty($upcoming_exams)): ?>
            <div class="empty-state" style="padding:30px">
                <div class="empty-icon">📅</div>
                <p>No upcoming exams scheduled</p>
                <a href="<?= BASE_URL ?>modules/exams/add.php" class="btn btn-primary btn-sm">Schedule Exam</a>
            </div>
        <?php else: ?>
        <table>
            <thead><tr><th>Exam</th><th>Date</th><th>Batches</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($upcoming_exams as $e): ?>
            <tr>
                <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
                <td><?= date('d M Y', strtotime($e['exam_date'])) ?></td>
                <td><span class="badge badge-primary"><?= $e['batch_count'] ?> batches</span></td>
                <td>
                    <?php if ($e['status'] == 'published'): ?>
                        <span class="badge badge-success">Published</span>
                    <?php elseif ($e['status'] == 'generated'): ?>
                        <span class="badge badge-warning">Generated</span>
                    <?php else: ?>
                        <span class="badge badge-gray">Pending</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h4><i class="fa-solid fa-bolt" style="color:#F59E0B"></i> Quick Actions</h4>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <a href="<?= BASE_URL ?>modules/batches/add.php" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add Batch
            </a>
            <a href="<?= BASE_URL ?>modules/students/add.php" class="btn btn-success">
                <i class="fa fa-plus"></i> Add Student
            </a>
            <a href="<?= BASE_URL ?>modules/rooms/add.php" class="btn btn-warning">
                <i class="fa fa-plus"></i> Add Room
            </a>
            <a href="<?= BASE_URL ?>modules/teachers/add.php" class="btn btn-secondary">
                <i class="fa fa-plus"></i> Add Teacher
            </a>
            <a href="<?= BASE_URL ?>modules/exams/add.php" class="btn btn-danger" style="grid-column:span 2">
                <i class="fa fa-calendar-plus"></i> Schedule New Exam
            </a>
        </div>

        <hr style="margin:20px 0; border:none; border-top:1px solid #E2E8F0">

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <a href="<?= BASE_URL ?>modules/seat_plan/index.php" class="btn btn-outline">
                <i class="fa fa-table-cells"></i> View Seat Plans
            </a>
            <a href="<?= BASE_URL ?>modules/duty_plan/index.php" class="btn btn-outline">
                <i class="fa fa-clipboard-list"></i> View Duty Plans
            </a>
        </div>
    </div>
</div>

</div>

<!-- Recent Exams Table -->
<div class="card" style="margin-top:22px">
    <div class="card-header">
        <h4><i class="fa-solid fa-clock-rotate-left" style="color:#4F46E5"></i> Recent Exams</h4>
    </div>
    <div class="table-wrapper">
        <?php if (empty($recent_exams)): ?>
            <div class="empty-state"><p>No exams created yet.</p></div>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Exam Title</th><th>Date</th><th>Time</th><th>Seats Generated</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($recent_exams as $i => $e): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
                <td><?= date('d M Y', strtotime($e['exam_date'])) ?></td>
                <td><?= date('h:i A', strtotime($e['start_time'])) ?> - <?= date('h:i A', strtotime($e['end_time'])) ?></td>
                <td><?= $e['seat_count'] ?> seats</td>
                <td>
                    <?php if ($e['status'] == 'published'): ?>
                        <span class="badge badge-success">Published</span>
                    <?php elseif ($e['status'] == 'generated'): ?>
                        <span class="badge badge-warning">Generated</span>
                    <?php else: ?>
                        <span class="badge badge-gray">Pending</span>
                <?php endif; ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>modules/seat_plan/view.php?exam_id=<?= $e['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-eye"></i> View
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
