<?php 
$page_title = 'Dashboard'; 


require_once 'includes/header.php'; 


try {
    // Ambil 5 artikel terbaru dengan data penulis dan kategori
    $stmt_articles = $pdo->query("
        SELECT a.id_article, a.title, a.status, a.created_at, c.name as category_name, u.username as author_name 
        FROM articles a
        LEFT JOIN categories c ON a.id_category = c.id_category
        LEFT JOIN users u ON a.id_user = u.id_user
        ORDER BY a.created_at DESC 
        LIMIT 5
    ");
    $recent_articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);

    // Hitung total artikel
    $total_articles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    // Hitung total pengguna
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
} catch (PDOException $e) {
    // Jika ada error, tampilkan pesan dan hentikan halaman
    echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<section class="mb-4">
    <h5 class="mb-3">Statistik Website</h5>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card card-statistic card-statistic-1">
                <h3><?php echo $total_articles; ?></h3>
                <p class="mb-0">Total Artikel</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-statistic card-statistic-2">
                <h3><?php echo $total_users; ?></h3>
                <p class="mb-0">Total Pengguna</p>
            </div>
        </div>
    </div>
</section>

<section class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Artikel Terbaru</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_articles): ?>
                        <?php foreach ($recent_articles as $article): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($article['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($article['author_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($article['category_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge badge-status <?php echo htmlspecialchars($article['status']); ?>">
                                        <?php echo ucfirst($article['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($article['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center p-4">Belum ada artikel.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php 
require_once 'includes/footer.php'; 
?>