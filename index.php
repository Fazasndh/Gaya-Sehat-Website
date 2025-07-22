<?php
session_start(); 
require 'config/database.php';

// Initialize default values
$articles = [];
$error_message = '';

try {
    // Query untuk mengambil 3 artikel terbaru yang sudah di-publish
    $sql = "SELECT * FROM articles 
            WHERE status = 'published'
            ORDER BY created_at DESC 
            LIMIT 3";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $articles = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // Jika terjadi error, catat ke log server
    $error_message = "Database Error: " . $e->getMessage();
    error_log($error_message);
}

// Helper function untuk membuat kutipan singkat dari artikel
function create_excerpt($text, $length = 100) {
    if (strlen($text) > $length) {
        $text = strip_tags($text);
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= '...';
    }
    return $text;
}

// Menyiapkan judul halaman sebelum memanggil header
$page_title = 'Gaya Sehat - Mulai Hidup Sehatmu Hari Ini';
include 'includes/header_user.php'; 
?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Opsi untuk IntersectionObserver
        const options = {
            root: null, // null berarti viewport browser
            rootMargin: '0px',
            threshold: 0.1 // elemen dianggap terlihat jika 10% areanya masuk layar
        };

        // Fungsi yang akan dijalankan saat elemen terlihat
        const callback = (entries, observer) => {
    entries.forEach(entry => {
        // Jika elemen masuk ke dalam layar
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
        } 
        // Jika elemen KELUAR dari layar
        else {
            entry.target.classList.remove('is-visible');
        }
    });
};

        // Buat observer baru
        const observer = new IntersectionObserver(callback, options);

        // Ambil semua elemen .feature-card dan mulai amati mereka
        const targets = document.querySelectorAll('.feature-card');
        targets.forEach(target => {
            observer.observe(target);
        });
    });
    </script>

</body>
</html>
<section class="hero-landing">
    <div class="container">
        <h1 class="hero-title">Selamat Datang di Gaya Sehat!</h1>
        <p class="hero-subtitle">Yuk, ubah kebiasaan sehari-hari jadi lebih baik!<br>Temukan inspirasi, informasi, dan tips seputar gaya hidup sehat yang mudah diterapkan dalam kehidupanmu.</p>
        <a href="articles.php" class="btn-primary-hero">Jelajahi Sekarang</a>
    </div>
</section>


<section class="features-section">
    <div class="container">
        <h2 class="section-title">Kenapa Memilih Gaya Sehat?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-newspaper"></i></div>
                <h3>Artikel Sehat</h3>
                <p>Temukan berbagai informasi terpercaya seputar kesehatan, mulai dari pencegahan penyakit hingga kesehatan mental.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-utensils"></i></div>
                <h3>Resep Sehat</h3>
                <p>Kumpulan resep makanan sehat dan lezat yang disajikan dengan panduan praktis untuk menjaga pola makan sehatmu.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-calculator"></i></div>
                <h3>Kalkulator BMI</h3>
                <p>Cek indeks massa tubuhmu secara instan sebagai langkah awal untuk mengetahui status berat badan idealmu.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-lightbulb"></i></div> <h3>Tips Gaya Hidup</h3>
                <p>Panduan sederhana untuk membangun kebiasaan sehat, mulai dari pola tidur, aktivitas fisik, hingga manajemen stres.</p>
            </div>
        </div>
    </div>
</section>


<section class="article-preview-section">
    <div class="container">
        <h2 class="section-title">Baca Artikel Terbaru Kami</h2>
        <div class="card-grid">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="content-card">
                        <div class="card-image" style="background-image: url('assets/uploads/<?php echo htmlspecialchars($article['image']); ?>');"></div>
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p><?php echo htmlspecialchars(create_excerpt($article['content'])); ?></p>
                            <a href="article_detail.php?id=<?php echo $article['id_article']; ?>" class="read-more-link">Baca Selengkapnya →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                 <div class="empty-state">
                    <p>Artikel terbaru akan segera hadir. Nantikan update dari kami!</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="section-footer">
            <a href="articles.php" class="btn-secondary">Lihat Semua Artikel</a>
        </div>
    </div>
</section>


<section class="cta-section">
    <div class="container">
        <h2>Siap Memulai Perjalanan Sehat Anda?</h2>
        <p>Bergabunglah dengan ribuan pengguna lainnya dan dapatkan akses ke semua fitur secara gratis.</p>
        <a href="register.php" class="btn-cta-final">Register Gratis Sekarang</a>
    </div>
</section>


<?php include 'includes/footer.php'; ?>