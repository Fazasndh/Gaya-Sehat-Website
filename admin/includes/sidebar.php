<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar bg-dark text-white p-3">
    <div class="sidebar-header mb-3 text-center">
        <a href="dashboard_admin.php" class="sidebar-title text-white text-decoration-none">
            <img src="../assets/image/logo_website.png" alt="Logo Gaya Sehat" class="sidebar-logo-img">
            <h5 class="mt-2 mb-0">Admin Panel</h5>
        </a>
    </div>
    <hr class="text-secondary">
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard_admin.php" class="nav-link text-white <?php if ($currentPage == 'dashboard_admin.php') echo 'active'; ?>">
                    <span class="sidebar-icon"><i class="fas fa-tachometer-alt fa-fw"></i></span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="manage_articles.php" class="nav-link text-white <?php if (in_array($currentPage, ['manage_articles.php', 'add_article.php', 'edit_article.php'])) echo 'active'; ?>">
                    <span class="sidebar-icon"><i class="fas fa-newspaper fa-fw"></i></span>
                    <span>Manajemen Artikel</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="manage_users.php" class="nav-link text-white <?php if (in_array($currentPage, ['manage_users.php', 'add_user.php', 'update_user.php'])) echo 'active'; ?>">
                    <span class="sidebar-icon"><i class="fas fa-users fa-fw"></i></span>
                    <span>Manajemen Pengguna</span>
                </a>
            </li>
            <li class="nav-item mt-auto">
                <hr class="text-secondary">
                <a href="../dashboard_user.php" class="nav-link text-white" target="_blank">
                    <span class="sidebar-icon"><i class="fas fa-globe fa-fw"></i></span>
                    <span>Lihat Website</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../logout.php" class="nav-link text-white">
                    <span class="sidebar-icon"><i class="fas fa-sign-out-alt fa-fw"></i></span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>