<?php 
// 1. Menentukan judul halaman ini
$page_title = 'Manajemen Artikel'; 

// 2. Memanggil file header (ini akan otomatis memuat semua yang kita butuhkan)
require_once 'includes/header.php'; 

?>

<?php
// --- Blok untuk menampilkan notifikasi sukses/error ---
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
    if ($status == 'deleted') {
        echo '<div class="alert alert-success">Artikel berhasil dihapus.</div>';
    } elseif ($status == 'added') {
        echo '<div class="alert alert-success">Artikel baru berhasil ditambahkan.</div>';
    } elseif ($status == 'updated') {
        echo '<div class="alert alert-success">Artikel berhasil diperbarui.</div>';
    } elseif ($status == 'error') {
        echo '<div class="alert alert-danger">Terjadi kesalahan: ' . $message . '</div>';
    }
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Daftar Semua Artikel</h5>
        <a href="add_article.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Artikel Baru
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal Terbit</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Query untuk mengambil SEMUA artikel dengan data kategori dan penulisnya
                    try {
                        $stmt = $pdo->query("
                            SELECT 
                                a.id_article, a.title, a.status, a.created_at, 
                                c.name as category_name, 
                                u.username as author_name
                            FROM articles a
                            LEFT JOIN categories c ON a.id_category = c.id_category
                            LEFT JOIN users u ON a.id_user = u.id_user
                            ORDER BY a.created_at DESC
                        ");
                        $all_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($all_articles) {
                            foreach ($all_articles as $index => $article) { ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($article['title']); ?></td>
                                    <td><?php echo htmlspecialchars($article['author_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($article['category_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge badge-status <?php echo htmlspecialchars($article['status']); ?>">
                                            <?php echo ucfirst($article['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($article['created_at'])); ?></td>
                                    <td class="text-center">
                                        <a href="update_article.php?id=<?php echo $article['id_article']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_article.php?id=<?php echo $article['id_article']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } else {
                            echo '<tr><td colspan="7" class="text-center p-4">Belum ada artikel yang dibuat.</td></tr>';
                        }
                    } catch (PDOException $e) {
                        echo '<tr><td colspan="7" class="text-center p-4 text-danger">Gagal mengambil data artikel: ' . $e->getMessage() . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
// 3. Memanggil footer untuk menutup semua tag HTML
require_once 'includes/footer.php'; 
?>