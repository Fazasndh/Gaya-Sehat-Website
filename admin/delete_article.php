<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Pastikan hanya admin yang bisa mengakses
require_admin();

// 1. Cek apakah ID artikel ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_articles.php');
    exit;
}

$id_article = $_GET['id'];

try {
    // 2. PENTING: Ambil nama file gambar sebelum menghapus data dari DB
    $stmt_select = $pdo->prepare("SELECT image FROM articles WHERE id_article = ?");
    $stmt_select->execute([$id_article]);
    $article = $stmt_select->fetch();

    if ($article) {
        $image_filename = $article['image'];

        // 3. Hapus data artikel dari database
        $stmt_delete = $pdo->prepare("DELETE FROM articles WHERE id_article = ?");
        $stmt_delete->execute([$id_article]);

        // 4. Jika data di DB berhasil dihapus, hapus juga file gambarnya dari server
        if ($stmt_delete->rowCount() > 0 && !empty($image_filename)) {
            $image_path = '../assets/uploads/' . $image_filename;
            if (file_exists($image_path)) {
                unlink($image_path); 
            }
        }
    }

    // 5. Redirect kembali ke halaman manajemen dengan pesan sukses
    header("Location: manage_articles.php?status=deleted");
    exit;

} catch (PDOException $e) {
    // Jika ada error, redirect dengan pesan error
    header("Location: manage_articles.php?status=error&message=" . urlencode($e->getMessage()));
    exit;
}
?>