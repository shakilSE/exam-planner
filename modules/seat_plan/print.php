<?php
require_once '../../config/db.php';
$exam_id = (int)($_GET['exam_id'] ?? 0);
$exam = $conn->query("SELECT * FROM exams WHERE id=$exam_id")->fetch_assoc();
if (!$exam) die("Exam not found");

$rooms_used = $conn->query("SELECT DISTINCT r.* FROM rooms r 
    JOIN seat_plan sp ON r.id=sp.room_id WHERE sp.exam_id=$exam_id ORDER BY r.room_no")->fetch_all(MYSQLI_ASSOC);

$all_batches = $conn->query("SELECT DISTINCT b.id, b.name FROM batches b 
    JOIN students s ON b.id=s.batch_id 
    JOIN seat_plan sp ON s.id=sp.student_id WHERE sp.exam_id=$exam_id")->fetch_all(MYSQLI_ASSOC);
$batch_colors = ['#dbeafe','#dcfce7','#fef9c3','#fee2e2','#ede9fe','#e0f2fe'];
$batch_color_map = [];
foreach ($all_batches as $idx => $b) {
    $batch_color_map[$b['id']] = $batch_colors[$idx % count($batch_colors)];
}

$duty_plans = $conn->query("SELECT dp.room_id, t.name FROM duty_plan dp 
    JOIN teachers t ON dp.teacher_id=t.id WHERE dp.exam_id=$exam_id")->fetch_all(MYSQLI_ASSOC);
$duty_map = [];
foreach ($duty_plans as $dp) { $duty_map[$dp['room_id']] = $dp['name']; }

$batch_names = $conn->query("SELECT b.name FROM batches b 
    JOIN exam_batches eb ON b.id=eb.batch_id WHERE eb.exam_id=$exam_id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Seat Plan — <?= htmlspecialchars($exam['title']) ?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, sans-serif; font-size:11px; color:#111; background:#fff; }
.print-header { text-align:center; padding:15px; border-bottom:2px solid #333; margin-bottom:15px; }
.print-header h1 { font-size:16px; }
.print-header p { font-size:12px; color:#555; margin-top:4px; }
.room-section { page-break-inside:avoid; margin-bottom:25px; }
.room-title { background:#1E293B; color:white; padding:8px 12px; font-size:12px; font-weight:bold; border-radius:4px; margin-bottom:10px; display:flex; justify-content:space-between; }
.seat-grid { display:grid; gap:4px; }
.seat-cell { border:1px solid #CBD5E1; border-radius:4px; padding:5px 4px; text-align:center; font-size:9.5px; min-height:55px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px; }
.seat-cell.empty { background:#F8FAFC; border-style:dashed; color:#aaa; }
.seat-no { font-size:8.5px; font-weight:bold; background:#1E293B; color:white; padding:1px 5px; border-radius:8px; }
.roll { font-weight:bold; font-size:10px; }
.sname { font-size:9px; color:#555; }
.legend { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:15px; padding:8px; border:1px solid #ddd; border-radius:4px; }
.leg-item { display:flex; align-items:center; gap:5px; font-size:10px; }
.leg-color { width:14px; height:14px; border-radius:3px; border:1px solid #ccc; }
table.duty-table { width:100%; border-collapse:collapse; margin-top:10px; }
table.duty-table th, table.duty-table td { border:1px solid #ddd; padding:6px 10px; font-size:11px; }
table.duty-table th { background:#f1f5f9; }
.col-header { text-align:center; font-size:9px; color:#777; font-weight:bold; padding:2px 0; }
.row-label { display:flex; align-items:center; justify-content:center; font-size:9px; color:#777; font-weight:bold; }
.no-print { margin:20px; }
@media print { .no-print { display:none; } }
</style>
</head>
<body>

<div class="no-print" style="padding:15px;background:#f8fafc;border-bottom:1px solid #ddd;display:flex;gap:10px">
    <button onclick="window.print()" style="background:#4F46E5;color:white;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-size:14px">🖨️ Print / Save as PDF</button>
    <button onclick="window.close()" style="background:#64748B;color:white;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-size:14px">✕ Close</button>
</div>

<div style="padding:20px">

<div class="print-header">
    <h1>EXAMINATION SEAT PLAN</h1>
    <p><?= htmlspecialchars($exam['title']) ?></p>
    <p>Date: <?= date('d F Y', strtotime($exam['exam_date'])) ?> | Time: <?= date('h:i A', strtotime($exam['start_time'])) ?> — <?= date('h:i A', strtotime($exam['end_time'])) ?></p>
    <p>Batches: <?= implode(', ', array_column($batch_names, 'name')) ?></p>
    <p style="font-size:10px;color:#777;margin-top:5px">Generated on: <?= date('d M Y, h:i A') ?></p>
</div>

<!-- Batch Legend -->
<div class="legend">
    <strong>Batches: </strong>
    <?php foreach ($all_batches as $idx => $b): ?>
        <div class="leg-item">
            <div class="leg-color" style="background:<?= $batch_colors[$idx % count($batch_colors)] ?>"></div>
            <?= htmlspecialchars($b['name']) ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Seat Plans per Room -->
<?php foreach ($rooms_used as $room): ?>
<?php
$seats = $conn->query("SELECT sp.*, s.roll, s.name as sname, s.batch_id
    FROM seat_plan sp JOIN students s ON sp.student_id=s.id
    WHERE sp.exam_id=$exam_id AND sp.room_id={$room['id']}
    ORDER BY sp.seat_row, sp.seat_col")->fetch_all(MYSQLI_ASSOC);
$grid = [];
foreach ($seats as $seat) { $grid[$seat['seat_row']][$seat['seat_col']] = $seat; }
?>
<div class="room-section">
    <div class="room-title">
        <span>Room <?= $room['room_no'] ?> <?= $room['building'] ? '('.$room['building'].')' : '' ?> — <?= count($seats) ?> Students</span>
        <span>Invigilator: <?= htmlspecialchars($duty_map[$room['id']] ?? 'Not Assigned') ?></span>
    </div>

    <!-- Column headers -->
    <div style="display:grid;grid-template-columns:22px repeat(<?= $room['cols'] ?>, 1fr);gap:3px;margin-bottom:3px">
        <div></div>
        <?php for ($c=1; $c<=$room['cols']; $c++): ?>
            <div class="col-header">C<?= $c ?></div>
        <?php endfor; ?>
    </div>

    <?php for ($r=1; $r<=$room['rows']; $r++): ?>
    <div style="display:grid;grid-template-columns:22px repeat(<?= $room['cols'] ?>, 1fr);gap:3px;margin-bottom:3px">
        <div class="row-label">R<?= $r ?></div>
        <?php for ($c=1; $c<=$room['cols']; $c++): ?>
            <?php if (isset($grid[$r][$c])): ?>
                <?php $s=$grid[$r][$c]; $bg=$batch_color_map[$s['batch_id']]??'#f8fafc'; ?>
                <div class="seat-cell" style="background:<?= $bg ?>">
                    <span class="seat-no"><?= $s['seat_no'] ?></span>
                    <span class="roll"><?= htmlspecialchars($s['roll']) ?></span>
                    <span class="sname"><?= htmlspecialchars(substr($s['sname'],0,12)) ?></span>
                </div>
            <?php else: ?>
                <div class="seat-cell empty">—</div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endfor; ?>
</div>
<?php endforeach; ?>

<!-- Duty Plan Summary Table -->
<div style="page-break-before:always">
    <h2 style="font-size:14px;margin-bottom:10px;padding:8px 0;border-bottom:2px solid #333">INVIGILATOR DUTY PLAN</h2>
    <table class="duty-table">
        <thead><tr><th>#</th><th>Room</th><th>Building</th><th>Invigilator</th><th>Students</th></tr></thead>
        <tbody>
        <?php foreach ($rooms_used as $i => $room): ?>
        <?php $count = $conn->query("SELECT COUNT(*) FROM seat_plan WHERE exam_id=$exam_id AND room_id={$room['id']}")->fetch_row()[0]; ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td>Room <?= $room['room_no'] ?></td>
            <td><?= $room['building'] ?? '-' ?></td>
            <td><?= htmlspecialchars($duty_map[$room['id']] ?? 'Not Assigned') ?></td>
            <td><?= $count ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>
</body>
</html>
