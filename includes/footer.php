<?php
if (!isset($beranda_url)) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $beranda_url = isset($_SESSION['user_id']) ? 'dashboard_user.php' : 'index.php';
}
?>

<footer class="footer-minimal">
    <div class="footer-top">
        <div class="social-icons">
            <a href="https://www.facebook.com/gayasehat" class="social-icon facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://wa.me/6285647137314" class="social-icon whatsapp" target="_blank"><i class="fab fa-whatsapp"></i></a>
            <a href="https://www.instagram.com/fazasndh" class="social-icon instagram" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://www.twitter.com/gayasehat" class="social-icon twitter" target="_blank"><i class="fab fa-twitter"></i></a>
        </div>
        <div class="footer-links main-links">
            <a href="<?php echo htmlspecialchars($beranda_url); ?>">Beranda</a>
            <a href="articles.php">Artikel</a>
            <a href="about.php">Tentang Kami</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="bmi_calculator.php">Kalkulator BMI</a>
                <a href="profile.php">Profil</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Gaya Sehat. Semua Hak Dilindungi.</p>
        <p>Sehat itu Mahal, Tapi Lebih Mahal Kalau Sakit <i class="fas fa-heart footer-heart"></i></p>
    </div>
</footer>

</body>
</html>