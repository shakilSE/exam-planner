<?php
require_once '../../config/db.php';
$page_title = 'Import Students';
$error = ''; $success = '';
$batches = $conn->query("SELECT * FROM batches ORDER BY name")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv'])) {
    $batch_id = (int)$_POST['batch_id'];
    $file = $_FILES['csv']['tmp_name'];
    if ($file && $batch_id) {
        $handle = fopen($file, 'r');
        $count = 0; $skip = 0;
        fgetcsv($handle); // skip header
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2) {
                $roll = sanitize($conn, trim($row[0]));
                $name = sanitize($conn, trim($row[1]));
                $email = isset($row[2]) ? sanitize($conn, trim($row[2])) : '';
                if ($roll && $name) {
                    $check = $conn->query("SELECT id FROM students WHERE roll='$roll'")->num_rows;
                    if ($check == 0) {
                        $conn->query("INSERT INTO students (roll,name,batch_id,email) VALUES ('$roll','$name',$batch_id,'$email')");
                        $count++;
                    } else { $skip++; }
                }
            }
        }
        fclose($handle);
        $success = "$count students imported successfully! ($skip skipped - duplicate rolls)";
    } else { $error = "Please select a file and batch."; }
}
include '../../includes/header.php';
?>
<div class="page-title"><div><h2>Import Students (CSV)</h2></div>
    <a href="index.php" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Back</a></div>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px">
    <div class="card">
        <div class="card-header"><h4><i class="fa fa-file-import"></i> Upload CSV File</h4></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group"><label>Select Batch *</label>
                    <select name="batch_id" class="form-control" required>
                        <option value="">Select Batch</option>
                        <?php foreach ($batches as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>CSV File *</label>
                    <input type="file" name="csv" class="form-control" accept=".csv" required></div>
                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-upload"></i> Import Now</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h4><i class="fa fa-info-circle"></i> CSV Format Guide</h4></div>
        <div class="card-body">
            <p style="margin-bottom:12px;color:#64748B;font-size:13px">Your CSV file should have this format:</p>
            <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;padding:15px;font-family:monospace;font-size:13px">
                <div style="color:#10B981;font-weight:700">roll,name,email</div>
                <div>CSE-2021-001,Rahim Ahmed,rahim@uni.edu</div>
                <div>CSE-2021-002,Karim Hossain,karim@uni.edu</div>
                <div>CSE-2021-003,Sadia Islam,sadia@uni.edu</div>
            </div>
            <ul style="margin-top:15px;padding-left:18px;font-size:13px;color:#64748B;line-height:2">
                <li>First row = header (will be skipped)</li>
                <li>Roll & Name are required</li>
                <li>Email is optional</li>
                <li>Duplicate rolls will be skipped</li>
            </ul>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
