<?php
requireLogin();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' — ' : '' ?>Exam Planner</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="app-wrapper">

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">📋</div>
        <div class="brand-text">
            <h2>Exam Planner</h2>
            <span>University System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">Main</div>
        <a href="<?= BASE_URL ?>dashboard.php" class="<?= $current_page == 'dashboard' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-gauge"></i></span> Dashboard
        </a>

        <div class="nav-section-title">Setup</div>
        <a href="<?= BASE_URL ?>modules/batches/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'batches') !== false ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-users"></i></span> Batches
        </a>
        <a href="<?= BASE_URL ?>modules/students/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'students') !== false ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-user-graduate"></i></span> Students
        </a>
        <a href="<?= BASE_URL ?>modules/rooms/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'rooms') !== false ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-door-open"></i></span> Rooms
        </a>
        <a href="<?= BASE_URL ?>modules/teachers/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'teachers') !== false ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-chalkboard-teacher"></i></span> Teachers
        </a>

        <div class="nav-section-title">Exam</div>
        <a href="<?= BASE_URL ?>modules/exams/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'exams') !== false ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-calendar-days"></i></span> Exams
        </a>
        <a href="<?= BASE_URL ?>modules/seat_plan/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'seat_plan') !== false ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-table-cells"></i></span> Seat Plan
        </a>
        <a href="<?= BASE_URL ?>modules/duty_plan/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'duty_plan') !== false ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span> Duty Plan
        </a>

        <div class="nav-section-title">Account</div>
        <a href="<?= BASE_URL ?>logout.php">
            <span class="nav-icon"><i class="fa-solid fa-right-from-bracket"></i></span> Logout
        </a>
    </nav>
</aside>

<!-- Main Content -->
<div class="main-content">

<!-- Top Header -->
<header class="top-header">
    <div class="header-left">
        <h3><?= isset($page_title) ? $page_title : 'Dashboard' ?></h3>
        <p><?= date('l, d F Y') ?></p>
    </div>
    <div class="header-right">
        <div class="admin-badge">
            <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_name'], 0, 1)) ?></div>
            <span><?= htmlspecialchars($_SESSION['admin_name']) ?></span>
        </div>
    </div>
</header>

<div class="page-content">
