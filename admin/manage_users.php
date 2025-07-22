<?php 
// File: admin/manage_users.php (Versi Final)

$page_title = 'Manajemen Pengguna'; 
require_once 'includes/header.php'; 
?>

<div class="container-fluid mt-4">

    <?php
    // Blok untuk menampilkan notifikasi
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        if ($status == 'user_deleted') {
            echo '<div class="alert alert-success">Pengguna berhasil dihapus.</div>';
        } elseif ($status == 'user_added') {
            echo '<div class="alert alert-success">Pengguna baru berhasil ditambahkan.</div>';
        } elseif ($status == 'user_updated') {
            echo '<div class="alert alert-success">Data pengguna berhasil diperbarui.</div>';
        } elseif ($status == 'delete_self_error') {
            echo '<div class="alert alert-danger">Anda tidak dapat menghapus akun Anda sendiri.</div>';
        }
    }
    ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Semua Pengguna</h5>
            <a href="add_user.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Tambah Pengguna Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Bergabung</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        try {
                            // Ambil semua pengguna kecuali admin yang sedang login
                            $current_admin_id = $_SESSION['user_id'];
                            $stmt = $pdo->prepare("SELECT id_user, username, email, role, created_at FROM users WHERE id_user != ? ORDER BY created_at DESC");
                            $stmt->execute([$current_admin_id]);
                            $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($all_users) {
                                foreach ($all_users as $index => $user) { ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                        <td class="text-center">
                                            <a href="update_user.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-user-edit"></i>
                                            </a>
                                            <a href="delete_user.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('PERINGATAN: Menghapus pengguna juga akan menghapus semua data terkait. Apakah Anda yakin?');">
                                                <i class="fas fa-user-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                echo '<tr><td colspan="6" class="text-center p-4">Tidak ada pengguna lain selain Anda.</td></tr>';
                            }
                        } catch (PDOException $e) {
                            echo '<tr><td colspan="6" class="text-center p-4 text-danger">Gagal mengambil data pengguna: ' . $e->getMessage() . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
require_once 'includes/footer.php'; 
?>