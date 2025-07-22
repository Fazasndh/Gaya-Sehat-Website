<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Pastikan hanya admin yang bisa mengakses
require_admin();

// 1. Cek apakah ID pengguna ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}
$id_user_to_delete = $_GET['id'];
$current_admin_id = $_SESSION['user_id'];

// 2. PENTING: Mencegah admin menghapus akunnya sendiri
if ($id_user_to_delete == $current_admin_id) {
    header("Location: manage_users.php?status=delete_self_error");
    exit;
}

try {
    // Memulai transaction. Jika salah satu query gagal, semua akan dibatalkan.
    $pdo->beginTransaction();

    // 3. Ambil semua artikel yang ditulis oleh pengguna yang akan dihapus
    $stmt_get_articles = $pdo->prepare("SELECT id_article, image FROM articles WHERE id_user = ?");
    $stmt_get_articles->execute([$id_user_to_delete]);
    $articles_to_delete = $stmt_get_articles->fetchAll();

    // 4. Hapus file gambar dari setiap artikel
    if ($articles_to_delete) {
        foreach ($articles_to_delete as $article) {
            if (!empty($article['image'])) {
                $image_path = '../assets/uploads/' . $article['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
    }

    // 5. Hapus semua artikel yang ditulis oleh pengguna tersebut
    $stmt_delete_articles = $pdo->prepare("DELETE FROM articles WHERE id_user = ?");
    $stmt_delete_articles->execute([$id_user_to_delete]);
    
 

    // 6. Setelah semua data terkait dihapus, hapus pengguna itu sendiri
    $stmt_delete_user = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
    $stmt_delete_user->execute([$id_user_to_delete]);

    
    $pdo->commit();

    
    header("Location: manage_users.php?status=user_deleted");
    exit;

} catch (PDOException $e) {
    
    $pdo->rollBack();
    header("Location: manage_users.php?status=error&message=" . urlencode($e->getMessage()));
    exit;
}
?>