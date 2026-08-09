<?php
require_once '../../config/db.php';
$page_title = 'View Seat Plan';
$exam_id = (int)($_GET['exam_id'] ?? 0);
$exam = $conn->query("SELECT * FROM exams WHERE id=$exam_id")->fetch_assoc();
if (!$exam) { header("Location: index.php"); exit(); }

// Get seat plan grouped by room
$rooms_used = $conn->query("SELECT DISTINCT r.* FROM rooms r 
    JOIN seat_plan sp ON r.id=sp.room_id WHERE sp.exam_id=$exam_id ORDER BY r.room_no")->fetch_all(MYSQLI_ASSOC);

// Batch colors for visual distinction
$batch_colors = ['#EEF2FF','#ECFDF5','#FFFBEB','#FEF2F2','#F5F3FF','#F0F9FF'];
$batch_text_colors = ['#4F46E5','#10B981','#D97706','#EF4444','#7C3AED','#0EA5E9'];

// Get all batches to assign colors
$all_batches = $conn->query("SELECT DISTINCT b.id, b.name FROM batches b 
    JOIN students s ON b.id=s.batch_id 
    JOIN seat_plan sp ON s.id=sp.student_id WHERE sp.exam_id=$exam_id")->fetch_all(MYSQLI_ASSOC);
$batch_color_map = [];
foreach ($all_batches as $idx => $b) {
    $batch_color_map[$b['id']] = [
        'bg' => $batch_colors[$idx % count($batch_colors)],
        'text' => $batch_text_colors[$idx % count($batch_text_colors)],
        'name' => $b['name']
    ];
}

include '../../includes/header.php';
?>

<div class="page-title">
    <div>
        <h2>Seat Plan — <?= htmlspecialchars($exam['title']) ?></h2>
        <p><?= date('d F Y', strtotime($exam['exam_date'])) ?> | <?= date('h:i A', strtotime($exam['start_time'])) ?> - <?= date('h:i A', strtotime($exam['end_time'])) ?></p>
    </div>
    <div style="display:flex;gap:10px">
        <a href="generate.php?exam_id=<?= $exam_id ?>" class="btn btn-outline"><i class="fa fa-gear"></i> Regenerate</a>
        <a href="print.php?exam_id=<?= $exam_id ?>" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Print / PDF</a>
        <a href="../../modules/duty_plan/view.php?exam_id=<?= $exam_id ?>" class="btn btn-secondary"><i class="fa fa-clipboard-list"></i> Duty Plan</a>
    </div>
</div>

<!-- Batch Legend -->
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
        <strong style="font-size:13px">Batch Legend:</strong>
        <?php foreach ($batch_color_map as $bid => $bc): ?>
            <span style="background:<?= $bc['bg'] ?>;color:<?= $bc['text'] ?>;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">
                <?= htmlspecialchars($bc['name']) ?>
            </span>
        <?php endforeach; ?>
        <span style="background:#F8FAFC;border:1.5px dashed #CBD5E1;color:#94A3B8;padding:4px 12px;border-radius:20px;font-size:12px">Empty Seat</span>
    </div>
</div>

<!-- Total Stats -->
<div class="stats-grid" style="margin-bottom:20px">
    <?php
    $total_seated = $conn->query("SELECT COUNT(*) FROM seat_plan WHERE exam_id=$exam_id")->fetch_row()[0];
    $rooms_count = count($rooms_used);
    ?>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa fa-users" style="color:#10B981"></i></div>
        <div class="stat-info"><h3><?= $total_seated ?></h3><p>Students Seated</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa fa-door-open" style="color:#4F46E5"></i></div>
        <div class="stat-info"><h3><?= $rooms_count ?></h3><p>Rooms Used</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fa fa-users" style="color:#7C3AED"></i></div>
        <div class="stat-info"><h3><?= count($all_batches) ?></h3><p>Batches</p></div>
    </div>
</div>

<!-- Seat Grid Per Room -->
<?php foreach ($rooms_used as $room): ?>
<?php
$seats = $conn->query("SELECT sp.*, s.roll, s.name as sname, s.batch_id, b.name as batch_name
    FROM seat_plan sp 
    JOIN students s ON sp.student_id=s.id 
    JOIN batches b ON s.batch_id=b.id
    WHERE sp.exam_id=$exam_id AND sp.room_id={$room['id']}
    ORDER BY sp.seat_row, sp.seat_col")->fetch_all(MYSQLI_ASSOC);

// Build grid array
$grid = [];
foreach ($seats as $seat) {
    $grid[$seat['seat_row']][$seat['seat_col']] = $seat;
}

$duty = $conn->query("SELECT t.name FROM teachers t 
    JOIN duty_plan dp ON t.id=dp.teacher_id 
    WHERE dp.exam_id=$exam_id AND dp.room_id={$room['id']}")->fetch_assoc();
?>
<div class="card" style="margin-bottom:22px">
    <div class="card-header">
        <h4>
            <i class="fa fa-door-open" style="color:#4F46E5"></i>
            Room <?= htmlspecialchars($room['room_no']) ?>
            <?php if ($room['building']): ?>(<?= htmlspecialchars($room['building']) ?>)<?php endif; ?>
            — Capacity: <?= $room['capacity'] ?> | Seated: <?= count($seats) ?>
        </h4>
        <?php if ($duty): ?>
            <span style="font-size:13px;color:#64748B"><i class="fa fa-chalkboard-teacher"></i> Invigilator: <strong><?= htmlspecialchars($duty['name']) ?></strong></span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <!-- Column headers -->
        <div style="display:grid;grid-template-columns:30px repeat(<?= $room['cols'] ?>, 1fr);gap:6px;margin-bottom:8px">
            <div></div>
            <?php for ($c = 1; $c <= $room['cols']; $c++): ?>
                <div style="text-align:center;font-size:11px;font-weight:700;color:#94A3B8">C<?= $c ?></div>
            <?php endfor; ?>
        </div>

        <?php for ($r = 1; $r <= $room['rows']; $r++): ?>
        <div style="display:grid;grid-template-columns:30px repeat(<?= $room['cols'] ?>, 1fr);gap:6px;margin-bottom:6px">
            <div style="display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#94A3B8">R<?= $r ?></div>
            <?php for ($c = 1; $c <= $room['cols']; $c++): ?>
                <?php if (isset($grid[$r][$c])): ?>
                    <?php $s = $grid[$r][$c]; $bc = $batch_color_map[$s['batch_id']] ?? ['bg'=>'#F8FAFC','text'=>'#64748B']; ?>
                    <div class="seat-cell" style="background:<?= $bc['bg'] ?>;border-color:<?= $bc['text'] ?>33">
                        <span class="seat-no" style="background:<?= $bc['text'] ?>20;color:<?= $bc['text'] ?>"><?= $s['seat_no'] ?></span>
                        <span class="student-roll" style="color:<?= $bc['text'] ?>"><?= htmlspecialchars($s['roll']) ?></span>
                        <span class="student-name"><?= htmlspecialchars(substr($s['sname'], 0, 15)) ?></span>
                        <span class="batch-tag" style="background:<?= $bc['bg'] ?>;color:<?= $bc['text'] ?>"><?= htmlspecialchars(explode(' ', $s['batch_name'])[0]) ?></span>
                    </div>
                <?php else: ?>
                    <div class="seat-cell empty">
                        <span style="font-size:18px;opacity:0.3">🪑</span>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endfor; ?>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($rooms_used)): ?>
    <div class="empty-state">
        <div class="empty-icon">🪑</div>
        <h4>No Seat Plan Generated Yet</h4>
        <a href="generate.php?exam_id=<?= $exam_id ?>" class="btn btn-primary">Generate Now</a>
    </div>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
