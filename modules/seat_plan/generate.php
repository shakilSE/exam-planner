<?php
require_once '../../config/db.php';
$page_title = 'Generate Seat Plan';
$exam_id = (int)($_GET['exam_id'] ?? 0);
$exam = $conn->query("SELECT * FROM exams WHERE id=$exam_id")->fetch_assoc();
if (!$exam) { header("Location: ../exams/index.php"); exit(); }

$message = '';
$message_type = '';

// ============================================================
// CORE ALGORITHM: Generate Seat Plan
// Rule: No two students from the same batch sit side by side
// ============================================================
if (isset($_GET['generate'])) {

    // Step 1: Delete old seat & duty plan for this exam
    $conn->query("DELETE FROM seat_plan WHERE exam_id=$exam_id");
    $conn->query("DELETE FROM duty_plan WHERE exam_id=$exam_id");

    // Step 2: Get all batches in this exam
    $exam_batches = $conn->query("SELECT batch_id FROM exam_batches WHERE exam_id=$exam_id")->fetch_all(MYSQLI_ASSOC);
    $batch_ids = array_column($exam_batches, 'batch_id');

    if (empty($batch_ids)) {
        $message = "No batches assigned to this exam!";
        $message_type = "danger";
    } else {
        // Step 3: Get all students grouped by batch
        $batch_students = [];
        foreach ($batch_ids as $bid) {
            $students = $conn->query("SELECT id,roll,name FROM students WHERE batch_id=$bid ORDER BY roll")->fetch_all(MYSQLI_ASSOC);
            if (!empty($students)) {
                $batch_students[$bid] = $students;
            }
        }

        // Step 4: INTERLEAVE students from different batches
        // This ensures no two same-batch students sit adjacent
        $interleaved = [];
        $max = max(array_map('count', $batch_students));
        for ($i = 0; $i < $max; $i++) {
            foreach ($batch_students as $bid => $students) {
                if (isset($students[$i])) {
                    $interleaved[] = ['student' => $students[$i], 'batch_id' => $bid];
                }
            }
        }

        // Step 5: Get all rooms sorted by capacity
        $rooms = $conn->query("SELECT * FROM rooms ORDER BY capacity ASC")->fetch_all(MYSQLI_ASSOC);
        if (empty($rooms)) {
            $message = "No rooms available! Please add rooms first.";
            $message_type = "danger";
        } else {
            // Step 6: Assign students to rooms seat by seat
            $student_index = 0;
            $total_students = count($interleaved);

            foreach ($rooms as $room) {
                if ($student_index >= $total_students) break;

                $rows = $room['rows'];
                $cols = $room['cols'];
                $seat_no = 1;

                // Fill seats row by row, column by column
                for ($r = 1; $r <= $rows; $r++) {
                    for ($c = 1; $c <= $cols; $c++) {
                        if ($student_index >= $total_students) break 2;

                        $student = $interleaved[$student_index]['student'];
                        $sid = $student['id'];
                        $rid = $room['id'];
                        $seat_label = $room['room_no'] . '-' . str_pad($seat_no, 3, '0', STR_PAD_LEFT);

                        $conn->query("INSERT INTO seat_plan (exam_id,student_id,room_id,seat_row,seat_col,seat_no) 
                            VALUES ($exam_id,$sid,$rid,$r,$c,'$seat_label')");

                        $seat_no++;
                        $student_index++;
                    }
                }
            }

            // Step 7: Generate Duty Plan — distribute teachers fairly
            $teachers = $conn->query("SELECT * FROM teachers ORDER BY duty_count ASC")->fetch_all(MYSQLI_ASSOC);
            
            if (!empty($teachers)) {
                // Get rooms that actually have students
                $used_rooms = $conn->query("SELECT DISTINCT room_id FROM seat_plan WHERE exam_id=$exam_id")->fetch_all(MYSQLI_ASSOC);
                $used_room_ids = array_column($used_rooms, 'room_id');
                
                $teacher_index = 0;
                foreach ($used_room_ids as $room_id) {
                    $tid = $teachers[$teacher_index % count($teachers)]['id'];
                    $conn->query("INSERT INTO duty_plan (exam_id,teacher_id,room_id) VALUES ($exam_id,$tid,$room_id)");
                    // Update teacher duty count
                    $conn->query("UPDATE teachers SET duty_count=duty_count+1 WHERE id=$tid");
                    $teacher_index++;
                }
            }

            // Step 8: Update exam status
            $conn->query("UPDATE exams SET status='generated' WHERE id=$exam_id");

            $seated = $student_index;
            $remaining = $total_students - $seated;
            
            if ($remaining > 0) {
                $message = "⚠️ Seat plan generated! $seated students seated. $remaining students could NOT be seated (insufficient room capacity). Please add more rooms.";
                $message_type = "warning";
            } else {
                $message = "✅ Seat plan generated successfully! $seated students seated across " . count($used_room_ids) . " room(s). Invigilator duty also assigned.";
                $message_type = "success";
            }
        }
    }
}

// Get exam info
$batch_names = $conn->query("SELECT b.name FROM batches b 
    JOIN exam_batches eb ON b.id=eb.batch_id WHERE eb.exam_id=$exam_id")->fetch_all(MYSQLI_ASSOC);
$total_students_count = $conn->query("SELECT COUNT(DISTINCT s.id) FROM students s 
    JOIN exam_batches eb ON s.batch_id=eb.batch_id WHERE eb.exam_id=$exam_id")->fetch_row()[0] ?? 0;
$total_capacity = $conn->query("SELECT SUM(capacity) FROM rooms")->fetch_row()[0] ?? 0;
$already_generated = $conn->query("SELECT COUNT(*) FROM seat_plan WHERE exam_id=$exam_id")->fetch_row()[0];

include '../../includes/header.php';
?>

<div class="page-title">
    <div><h2>Generate Seat Plan</h2><p><?= htmlspecialchars($exam['title']) ?></p></div>
    <a href="../exams/index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back to Exams</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?>"><i class="fa fa-circle-info"></i> <?= $message ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px">
    <!-- Exam Info -->
    <div class="card">
        <div class="card-header"><h4><i class="fa fa-calendar-days" style="color:#4F46E5"></i> Exam Details</h4></div>
        <div class="card-body" style="font-size:14px;line-height:2.2">
            <div><strong>Title:</strong> <?= htmlspecialchars($exam['title']) ?></div>
            <div><strong>Date:</strong> <?= date('d F Y', strtotime($exam['exam_date'])) ?></div>
            <div><strong>Time:</strong> <?= date('h:i A', strtotime($exam['start_time'])) ?> — <?= date('h:i A', strtotime($exam['end_time'])) ?></div>
            <div><strong>Batches:</strong> 
                <?php foreach ($batch_names as $bn): ?>
                    <span class="badge badge-primary"><?= htmlspecialchars($bn['name']) ?></span>
                <?php endforeach; ?>
            </div>
            <div><strong>Total Students:</strong> <span class="badge badge-success"><?= $total_students_count ?></span></div>
            <div><strong>Total Capacity:</strong> <span class="badge badge-warning"><?= $total_capacity ?></span></div>
            <div><strong>Status:</strong> 
                <?php if ($exam['status']=='generated'): ?><span class="badge badge-warning">Generated</span>
                <?php elseif ($exam['status']=='published'): ?><span class="badge badge-success">Published</span>
                <?php else: ?><span class="badge badge-gray">Pending</span><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Algorithm Info -->
    <div class="card">
        <div class="card-header"><h4><i class="fa fa-wand-magic-sparkles" style="color:#F59E0B"></i> Algorithm Info</h4></div>
        <div class="card-body" style="font-size:13px;color:#64748B;line-height:2">
            <p style="margin-bottom:10px"><strong style="color:#1E293B">How the algorithm works:</strong></p>
            <ul style="padding-left:18px">
                <li>Students from different batches are <strong>interleaved</strong> before seating</li>
                <li>Pattern: <span style="color:#4F46E5">Batch-A → Batch-B → Batch-C → Batch-A...</span></li>
                <li>No two students from the same batch sit <strong>side by side</strong></li>
                <li>Teachers assigned based on <strong>lowest duty count first</strong> (fair distribution)</li>
                <li>Seats filled <strong>row by row</strong> across all rooms</li>
            </ul>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="card">
    <div class="card-header"><h4><i class="fa fa-gear"></i> Actions</h4></div>
    <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap">
        <a href="?exam_id=<?= $exam_id ?>&generate=1" class="btn btn-primary btn-lg"
           onclick="return confirm('<?= $already_generated ? 'This will REGENERATE and overwrite the existing seat plan. Continue?' : 'Generate seat plan now?' ?>')">
            <i class="fa fa-wand-magic-sparkles"></i> 
            <?= $already_generated ? 'Re-Generate Seat Plan' : 'Generate Seat Plan' ?>
        </a>

        <?php if ($already_generated > 0): ?>
        <a href="view.php?exam_id=<?= $exam_id ?>" class="btn btn-success btn-lg">
            <i class="fa fa-eye"></i> View Seat Plan
        </a>
        <a href="../../modules/duty_plan/view.php?exam_id=<?= $exam_id ?>" class="btn btn-secondary btn-lg">
            <i class="fa fa-clipboard-list"></i> View Duty Plan
        </a>
        <a href="print.php?exam_id=<?= $exam_id ?>" class="btn btn-warning btn-lg" target="_blank">
            <i class="fa fa-print"></i> Print / PDF
        </a>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
