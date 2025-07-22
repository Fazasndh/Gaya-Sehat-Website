<?php
// Pastikan sesi selalu aktif saat header dipanggil
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Menentukan nama halaman saat ini untuk menandai menu aktif
$currentPage = basename($_SERVER['PHP_SELF']);
// Menentukan URL dinamis untuk link Beranda dan Logo
$beranda_url = isset($_SESSION['user_id']) ? 'dashboard_user.php' : 'index.php';
// Menyiapkan judul halaman default jika tidak ditentukan
$page_title = $page_title ?? 'Gaya Sehat';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<header>
    <nav class="container">
        <a href="<?php echo htmlspecialchars($beranda_url); ?>" class="logo">
            <img src="assets/image/logo_website.png" height="40" alt="Logo Gaya Sehat">
        </a>
        <ul>
            <li><a href="<?php echo htmlspecialchars($beranda_url); ?>" class="<?php if (in_array($currentPage, ['index.php', 'dashboard_user.php'])) { echo 'active'; } ?>">Beranda</a></li>
            <li><a href="articles.php" class="<?php if($currentPage == 'articles.php' || $currentPage == 'article_detail.php') { echo 'active'; } ?>">Artikel</a></li>
            <li><a href="about.php" class="<?php if($currentPage == 'about.php') { echo 'active'; } ?>">Tentang</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="bmi_calculator.php" class="<?php if($currentPage == 'bmi_calculator.php') { echo 'active'; } ?>">BMI</a></li>
                <li><a href="profile.php" class="<?php if($currentPage == 'profile.php') { echo 'active'; } ?>">Profil</a></li>
                <li><a href="logout.php">Keluar</a></li>
            <?php else: ?>
                <li><a href="login.php" class="<?php if($currentPage == 'login.php') { echo 'active'; } ?>">Masuk</a></li>
                <li><a href="register.php" class="btn <?php if($currentPage == 'register.php') { echo 'active'; } ?>">Daftar</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>