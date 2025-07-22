<?php
require_once 'config/database.php';
require_once 'includes/functions.php'; 

// Ambil dan validasi ID artikel dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id_article = $_GET['id'];

// Query database untuk mengambil satu artikel berdasarkan ID
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.*, 
            c.name as category_name, 
            u.username as author_name
        FROM 
            articles a
        LEFT JOIN 
            categories c ON a.id_category = c.id_category
        LEFT JOIN 
            users u ON a.id_user = u.id_user
        WHERE 
            a.id_article = ? AND a.status = 'published'
    ");
    $stmt->execute([$id_article]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Di aplikasi production, ini sebaiknya dicatat ke log
    die("Error: Gagal mengambil data artikel. " . $e->getMessage());
}

// 3. Jika artikel dengan ID tersebut tidak ditemukan, tampilkan pesan
if (!$article) {
    $page_title = 'Artikel Tidak Ditemukan';
    require_once 'includes/header_user.php';
    echo "<div class='container' style='padding: 50px; text-align: center;'><h2>Maaf, artikel yang Anda cari tidak ditemukan.</h2><a href='index.php'>Kembali ke Beranda</a></div>";
    require_once 'includes/footer.php';
    exit;
}

// Menyiapkan judul halaman sebelum memanggil header
$page_title = htmlspecialchars($article['title']);
require_once 'includes/header_user.php';
?>

<div class="container article-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <article class="article-detail">
                <header class="article-detail-header">
                    <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="article-meta">
                        <span>Oleh: <strong><?php echo htmlspecialchars($article['author_name'] ?? 'Tim Redaksi'); ?></strong></span>
                        <span class="meta-separator">|</span>
                        <span>Kategori: <strong><?php echo htmlspecialchars($article['category_name'] ?? 'Tanpa Kategori'); ?></strong></span>
                        <span class="meta-separator">|</span>
                        <span>Diterbitkan: <?php echo date('d F Y', strtotime($article['created_at'])); ?></span>
                    </div>
                </header>

                <figure class="article-featured-image">
                    <img src="assets/uploads/<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                </figure>

                <section class="article-content">
                    <?php
                        // Menggunakan nl2br() untuk mengubah baris baru (\n) menjadi tag <br>
                        // Ini akan menjaga format paragraf dari textarea
                        echo nl2br($article['content']); 
                    ?>
                </section>
                
                <footer class="article-footer">
                    <a href="articles.php" class="btn-secondary">← Kembali ke Daftar Artikel</a>
                </footer>

            </article>

        </div>
    </div>
</div>

<?php 
require_once 'includes/footer.php'; 
?>