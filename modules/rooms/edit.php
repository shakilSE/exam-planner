<?php
require_once '../../config/db.php';
$page_title = 'Edit Room';
$id = (int)($_GET['id'] ?? 0);
$room = $conn->query("SELECT * FROM rooms WHERE id=$id")->fetch_assoc();
if (!$room) { header("Location: index.php"); exit(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_no = sanitize($conn, $_POST['room_no']);
    $building = sanitize($conn, $_POST['building']);
    $rows = (int)$_POST['rows'];
    $cols = (int)$_POST['cols'];
    $capacity = $rows * $cols;
    $conn->query("UPDATE rooms SET room_no='$room_no',building='$building',capacity=$capacity,`rows`=$rows,`cols`=$cols WHERE id=$id");
    $_SESSION['success'] = "Room updated!";
    header("Location: index.php"); exit();
}
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Edit Room</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:500px">
    <div class="card-header"><h4><i class="fa fa-pen"></i> Edit Room</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Room Number *</label>
                <input type="text" name="room_no" class="form-control" value="<?= htmlspecialchars($room['room_no']) ?>" required>
            </div>
            <div class="form-group">
                <label>Building Name</label>
                <input type="text" name="building" class="form-control" value="<?= htmlspecialchars($room['building'] ?? '') ?>">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
                <div class="form-group">
                    <label>Rows *</label>
                    <input type="number" name="rows" class="form-control" value="<?= $room['rows'] ?>" min="1" required>
                </div>
                <div class="form-group">
                    <label>Columns *</label>
                    <input type="number" name="cols" class="form-control" value="<?= $room['cols'] ?>" min="1" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Update Room</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
