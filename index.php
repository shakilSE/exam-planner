<?php
require_once 'config/db.php';

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && $user['password'] === md5($password)) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_email'] = $user['email'];
        header("Location: " . BASE_URL . "dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Northern University Bangladesh — Portal</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* আপনার আইডিয়া অনুযায়ী মেইন ফোল্ডারের লোকাল ছবি nub-campus.png এখানে ১০০% নিখুঁতভাবে কানেক্ট করা হয়েছে */
            background: linear-gradient(rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.65)), 
                        url('nub-campus.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .univ-navbar {
            background-color: rgba(15, 34, 64, 0.98); 
            border-bottom: 3px solid #1a365d;
            padding: 15px 0;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .nav-logo {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 25px;
            margin: 0;
            padding: 0;
        }
        .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #63b3ed;
        }
        .nav-search input {
            padding: 8px 15px;
            border: 1px solid #4a5568;
            border-radius: 20px;
            background-color: rgba(255,255,255,0.1);
            color: white;
            outline: none;
            width: 180px;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 380px;
            border-top: 5px solid #1a365d;
        }
        .login-card h1 {
            font-size: 24px;
            color: #1a365d;
            margin: 0 0 5px 0;
            text-align: center;
            font-weight: 700;
        }
        .subtitle {
            color: #64748b;
            font-size: 13px;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-primary {
            background-color: #1a365d;
            color: white;
            margin-bottom: 15px;
        }
        .btn-primary:hover { background-color: #2a4365; }

        .btn-student {
            background-color: #0284c7;
            color: white;
        }
        .btn-student:hover { background-color: #0369a1; }

        .divider {
            text-align: center;
            margin: 15px 0;
            color: #94a3b8;
            position: relative;
            font-size: 14px;
        }
        .divider:before, .divider:after {
            content: "";
            position: absolute;
            width: 42%;
            height: 1px;
            background: #cbd5e1;
            top: 50%;
        }
        .divider:before { left: 0; }
        .divider:after { right: 0; }

        .alert {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<nav class="univ-navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">
            <i class="fa-solid fa-graduation-cap"></i> Northern University Bangladesh
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="#" onclick="alert('About Exam Planner System:\n\nThis platform is managed by the Office of the Controller of Examinations, Northern University Bangladesh (NUB). It coordinates semester final seat allocations, room logistics, and automated invigilator/faculty duty rosters to ensure clash-free evaluations.')">About</a></li>
            <li><a href="#" onclick="alert('Academic System Status:\n\nSemester Final Schedule & Room Matrices are currently active.')">Academics</a></li>
            <li><a href="#" onclick="alert('Exam Control Support:\nEmail: examcell@nub.ac.bd\nHotline: +88017XXXXXXXX')">Contact</a></li>
        </ul>
        <div class="nav-search">
            <input type="text" placeholder="Search Notices..." onkeypress="if(event.key === 'Enter') alert('Searching exam records...');">
        </div>
    </div>
</nav>

<div class="main-content">
    <div class="login-card">
        <h1>Exam Planner</h1>
        <p class="subtitle">Seat & Invigilator Management System</p>

        <?php if ($error): ?>
            <div class="alert"><i class="fa fa-circle-exclamation"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label><i class="fa fa-envelope"></i> Authority Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@exam.com" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label><i class="fa fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-right-to-bracket"></i> Admin Sign In
            </button>
        </form>

        <div class="divider">OR</div>

        <button class="btn btn-student" onclick="alert('Student Portal Sync:\n\nEnter your NUB Student ID to view your allocated Room Number, Building, and Seat Row details.')">
            <i class="fa fa-user-graduate"></i> Student Seat Plan Login
        </button>
    </div>
</div>

</body>
</html>
