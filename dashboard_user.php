<?php
session_start();

// Keamanan: Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Inisialisasi array untuk data
$bmi_history = [];
$latest_articles = [];

try {
    // Ambil 3 data riwayat BMI terbaru
    $stmt_bmi = $pdo->prepare("SELECT * FROM bmi_history WHERE id_user = ? ORDER BY created_at DESC LIMIT 3");
    $stmt_bmi->execute([$user_id]);
    $bmi_history = $stmt_bmi->fetchAll();

    // Ambil 3 artikel terbaru yang statusnya sudah 'published'
$stmt_articles = $pdo->prepare("SELECT * FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
$stmt_articles->execute();
$latest_articles = $stmt_articles->fetchAll();

} catch (PDOException $e) {
    // Catat error ke log server untuk debugging, jangan tampilkan ke user
    error_log("Dashboard Query Error: " . $e->getMessage());
}

// Helper function untuk membuat kutipan singkat dari artikel
function create_excerpt($text, $length = 100) {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' ')); 
        $text .= '...';
    }
    return $text;
}


// Menyiapkan judul halaman untuk dikirim ke header
$page_title = 'Dashboard - ' . htmlspecialchars($username);

// Panggil header setelah semua logika selesai
require_once 'includes/header_user.php';
?>

<section class="dashboard-hero">
    <div class="container">
        <h1>Selamat Datang, <span class="animated-username"><?php echo htmlspecialchars($username); ?></span>!</h1>
        <p class="motivation-quote">"Tempat terbaik untuk memulai perjalanan hidup sehatmu. Kami percaya bahwa hidup sehat adalah hak setiap orang, dimulai dari langkah kecil yang konsisten."</p>
    </div>
</section>

<main class="container page-content">

    <section class="dashboard-section">
        <div class="section-header">
            <h2>Riwayat BMI Terbaru</h2>
        </div>
        <div class="card-grid">
            <?php if (!empty($bmi_history)): ?>
                <?php foreach ($bmi_history as $history): ?>
                    <div class="content-card">
                        <div class="card-image bmi-category-bg-<?php echo strtolower(explode(' ', $history['category'])[0]); ?>">
                            <span class="bmi-value-display"><?php echo htmlspecialchars($history['bmi_value']); ?></span>
                            <span class="bmi-label-display">BMI</span>
                        </div>
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($history['category']); ?></h3>
                            <p class="card-meta">Dicatat pada: <?php echo date('d M Y', strtotime($history['created_at'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>Anda belum memiliki riwayat BMI. Yuk, coba <a href="bmi_calculator.php">Kalkulator BMI</a> kami!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-header">
            <h2>Artikel Terbaru</h2>
            <a href="articles.php" class="view-all-link">Lihat Semua</a>
        </div>
        <div class="card-grid">
            <?php if (!empty($latest_articles)): ?>
                <?php foreach ($latest_articles as $article): ?>
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
                    <p>Belum ada artikel yang dipublikasikan. Nantikan update dari kami!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>


<?php 
require_once 'includes/footer.php'; 
?>