<?php
require_once '../../config/db.php';
$page_title = 'Add Room';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_no = sanitize($conn, $_POST['room_no']);
    $building = sanitize($conn, $_POST['building']);
    $rows = (int)$_POST['rows'];
    $cols = (int)$_POST['cols'];
    $capacity = $rows * $cols;
    if ($room_no && $rows && $cols) {
        $conn->query("INSERT INTO rooms (room_no, building, capacity, `rows`, `cols`) VALUES ('$room_no','$building',$capacity,$rows,$cols)");
        $_SESSION['success'] = "Room added!";
        header("Location: index.php"); exit();
    } else { $error = "Please fill required fields."; }
}
include '../../includes/header.php';
?>
<div class="page-title">
    <div><h2>Add Room</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<div class="card" style="max-width:500px">
    <div class="card-header"><h4><i class="fa fa-plus"></i> Room Details</h4></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Room Number *</label>
                <input type="text" name="room_no" class="form-control" placeholder="e.g. 101" required>
            </div>
            <div class="form-group">
                <label>Building Name</label>
                <input type="text" name="building" class="form-control" placeholder="e.g. Academic Block A">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px">
                <div class="form-group">
                    <label>Number of Rows *</label>
                    <input type="number" name="rows" class="form-control" min="1" max="20" placeholder="5" required>
                </div>
                <div class="form-group">
                    <label>Number of Columns *</label>
                    <input type="number" name="cols" class="form-control" min="1" max="20" placeholder="6" required>
                </div>
            </div>
            <div class="alert alert-warning" style="font-size:12px"><i class="fa fa-info-circle"></i> Capacity = Rows × Columns</div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save Room</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
