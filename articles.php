<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// --- LOGIKA PAGINATION (PENOMORAN HALAMAN) ---
$limit = 4; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- LOGIKA PENGAMBILAN DATA ARTIKEL ---
try {
    // Query untuk menghitung TOTAL artikel yang published (untuk pagination)
    $total_articles_stmt = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'");
    $total_articles = $total_articles_stmt->fetchColumn();
    $total_pages = ceil($total_articles / $limit);

    // Query UTAMA untuk mengambil artikel sesuai halaman saat ini
    $stmt = $pdo->prepare("
        SELECT 
            a.id_article, a.title, a.content, a.image, a.created_at, 
            c.name as category_name
        FROM 
            articles a
        LEFT JOIN 
            categories c ON a.id_category = c.id_category
        WHERE 
            a.status = 'published'
        ORDER BY 
            a.created_at DESC
        LIMIT :limit OFFSET :offset
    ");

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Articles page error: " . $e->getMessage());
    $articles = [];
    $total_pages = 0;
}

// Menyiapkan judul halaman
$page_title = 'Semua Artikel';
require_once 'includes/header_user.php';
?>

<main class="container page-content">
    <div class="page-header">
        <h1>Semua Artikel</h1>
        <p>Temukan informasi, tips, dan resep terbaru untuk mendukung gaya hidup sehat Anda.</p>
    </div>

    <div class="card-grid">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <div class="content-card">
                    <div class="card-image" style="background-image: url('assets/uploads/<?php echo htmlspecialchars($article['image']); ?>');">
                       
                    </div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p><?php echo htmlspecialchars(create_excerpt($article['content'], 80)); ?></p>
                        <a href="article_detail.php?id=<?php echo $article['id_article']; ?>" class="read-more-link">Baca Selengkapnya →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <p>Belum ada artikel yang dipublikasikan saat ini. Silakan kembali lagi nanti!</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="pagination-wrapper" aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                <a class="page-link" href="<?php if($page > 1){ echo '?page=' . ($page - 1); } else { echo '#'; } ?>">Sebelumnya</a>
            </li>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?php if($page >= $total_pages) { echo 'disabled'; } ?>">
                <a class="page-link" href="<?php if($page < $total_pages) { echo '?page=' . ($page + 1); } else { echo '#'; } ?>">Berikutnya</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</main>

<?php require_once 'includes/footer.php'; ?>