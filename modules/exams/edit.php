<?php
require_once '../../config/db.php';
$page_title = 'Edit Exam';
$id = (int)($_GET['id'] ?? 0);
$exam = $conn->query("SELECT * FROM exams WHERE id=$id")->fetch_assoc();
if (!$exam) { header("Location: index.php"); exit(); }
$batches = $conn->query("SELECT * FROM batches ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$selected_batches = array_column($conn->query("SELECT batch_id FROM exam_batches WHERE exam_id=$id")->fetch_all(MYSQLI_ASSOC), 'batch_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($conn, $_POST['title']);
    $date = sanitize($conn, $_POST['exam_date']);
    $start = sanitize($conn, $_POST['start_time']);
    $end = sanitize($conn, $_POST['end_time']);
    $batch_ids = $_POST['batch_ids'] ?? [];
    $conn->query("UPDATE exams SET title='$title',exam_date='$date',start_time='$start',end_time='$end' WHERE id=$id");
    $conn->query("DELETE FROM exam_batches WHERE exam_id=$id");
    foreach ($batch_ids as $bid) {
        $bid = (int)$bid;
        $conn->query("INSERT INTO exam_batches (exam_id,batch_id) VALUES ($id,$bid)");
    }
    $_SESSION['success'] = "Exam updated!";
    header("Location: index.php"); exit();
}
include '../../includes/header.php';
?>
<div class="page-title"><div><h2>Edit Exam</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a></div>
<div class="card" style="max-width:600px">
    <div class="card-header"><h4><i class="fa fa-pen"></i> Edit Exam Details</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group"><label>Exam Title *</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($exam['title']) ?>" required></div>
            <div class="form-group"><label>Exam Date *</label>
                <input type="date" name="exam_date" class="form-control" value="<?= $exam['exam_date'] ?>" required></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
                <div class="form-group"><label>Start Time</label>
                    <input type="time" name="start_time" class="form-control" value="<?= $exam['start_time'] ?>"></div>
                <div class="form-group"><label>End Time</label>
                    <input type="time" name="end_time" class="form-control" value="<?= $exam['end_time'] ?>"></div>
            </div>
            <div class="form-group">
                <label>Batches</label>
                <div style="border:1.5px solid #E2E8F0;border-radius:8px;padding:12px">
                    <?php foreach ($batches as $b): ?>
                    <label style="display:flex;align-items:center;gap:10px;padding:6px;cursor:pointer">
                        <input type="checkbox" name="batch_ids[]" value="<?= $b['id'] ?>" <?= in_array($b['id'], $selected_batches) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Update Exam</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
