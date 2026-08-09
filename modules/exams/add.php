<?php
require_once '../../config/db.php';
$page_title = 'Schedule Exam';
$error = '';
$batches = $conn->query("SELECT * FROM batches ORDER BY name")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($conn, $_POST['title']);
    $date = sanitize($conn, $_POST['exam_date']);
    $start = sanitize($conn, $_POST['start_time']);
    $end = sanitize($conn, $_POST['end_time']);
    $selected_batches = $_POST['batch_ids'] ?? [];

    if ($title && $date && $start && $end && !empty($selected_batches)) {
        $conn->query("INSERT INTO exams (title,exam_date,start_time,end_time) VALUES ('$title','$date','$start','$end')");
        $exam_id = $conn->insert_id;
        foreach ($selected_batches as $bid) {
            $bid = (int)$bid;
            $conn->query("INSERT INTO exam_batches (exam_id,batch_id) VALUES ($exam_id,$bid)");
        }
        $_SESSION['success'] = "Exam scheduled! Now generate seat plan.";
        header("Location: ../../modules/seat_plan/generate.php?exam_id=$exam_id"); exit();
    } else { $error = "Please fill all fields and select at least one batch."; }
}
include '../../includes/header.php';
?>
<div class="page-title"><div><h2>Schedule Exam</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a></div>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<div style="display:grid;grid-template-columns:2fr 1fr;gap:22px">
<div class="card">
    <div class="card-header"><h4><i class="fa fa-calendar-plus"></i> Exam Details</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group"><label>Exam Title *</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Final Examination - Fall 2024" required></div>
            <div class="form-group"><label>Exam Date *</label>
                <input type="date" name="exam_date" class="form-control" required min="<?= date('Y-m-d') ?>"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
                <div class="form-group"><label>Start Time *</label>
                    <input type="time" name="start_time" class="form-control" required value="09:00"></div>
                <div class="form-group"><label>End Time *</label>
                    <input type="time" name="end_time" class="form-control" required value="12:00"></div>
            </div>
            <div class="form-group">
                <label>Select Batches * (can select multiple)</label>
                <div style="border:1.5px solid #E2E8F0;border-radius:8px;padding:12px;max-height:200px;overflow-y:auto">
                    <?php foreach ($batches as $b): ?>
                    <label style="display:flex;align-items:center;gap:10px;padding:8px;cursor:pointer;border-radius:6px;transition:background 0.2s"
                           onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background=''">
                        <input type="checkbox" name="batch_ids[]" value="<?= $b['id'] ?>">
                        <span><strong><?= htmlspecialchars($b['name']) ?></strong> — <?= htmlspecialchars($b['department']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-calendar-check"></i> Schedule & Generate Seat Plan</button>
        </form>
    </div>
</div>
<div class="card" style="align-self:start">
    <div class="card-header"><h4><i class="fa fa-circle-info"></i> Info</h4></div>
    <div class="card-body" style="font-size:13px;color:#64748B;line-height:2">
        <p>✅ After scheduling, system will auto-generate:</p>
        <ul style="padding-left:18px;margin-top:8px">
            <li>Seat plan (no same batch side by side)</li>
            <li>Invigilator duty roster</li>
            <li>Printable PDF</li>
        </ul>
    </div>
</div>
</div>
<?php include '../../includes/footer.php'; ?>
