<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_admin();
$page_title = $page_title ?? 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Gaya Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="d-flex">
    <?php require_once 'sidebar.php'; ?>
    
    <div class="main-content-wrapper">
        <main class="p-4">
            <header class="main-header bg-white rounded shadow-sm p-3">
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-secondary d-lg-none me-3" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="h3 mb-0"><?php echo htmlspecialchars($page_title); ?></h1>
    </div>
    <div class="user-info">
        <span><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    </div>
</header>