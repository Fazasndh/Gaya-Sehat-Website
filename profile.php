<?php
session_start();

require 'config/database.php';
require 'includes/functions.php';


require_login(); 

$user_id = $_SESSION['user_id'];
$errors = [];
$success_msg = '';


try {
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id_user = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $errors[] = "Gagal mengambil data profil.";
    $user = ['username' => '', 'email' => '']; 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    
    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong.";
    }


    $stmt_check = $pdo->prepare("SELECT id_user FROM users WHERE username = ? AND id_user != ?");
    $stmt_check->execute([$username, $user_id]);
    if ($stmt_check->fetch()) {
        $errors[] = "Username tersebut sudah digunakan oleh pengguna lain.";
    }
    
    // Proses update jika tidak ada error
    if (empty($errors)) {
        $stmt_update_user = $pdo->prepare("UPDATE users SET username = ? WHERE id_user = ?");
        $stmt_update_user->execute([$username, $user_id]);
        $_SESSION['username'] = $username; 
        $user['username'] = $username; 
        $success_msg = "Profil berhasil diperbarui!";


        if (!empty($_POST['password'])) {
            if (empty($_POST['password_confirm'])) {
                $errors[] = "Konfirmasi password baru wajib diisi.";
            } elseif ($_POST['password'] !== $_POST['password_confirm']) {
                $errors[] = "Konfirmasi password baru tidak cocok.";
            } elseif (strlen($_POST['password']) < 6) {
                $errors[] = "Password baru minimal 6 karakter.";
            } else {
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt_pass = $pdo->prepare("UPDATE users SET password = ? WHERE id_user = ?");
                $stmt_pass->execute([$hashed_password, $user_id]);
                $success_msg .= " Password juga berhasil diubah.";
            }
        }
    }
}

$page_title = 'Edit Profil';
require_once 'includes/header_user.php';
?>

<main class="container page-content">
    <div class="page-header">
        <h1>Pengaturan Akun</h1>
        <p>Perbarui informasi profil dan keamanan akun Anda.</p>
    </div>

    <div class="profile-form-container">
        <form action="profile.php" method="POST">
            
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $error) echo "<div>$error</div>"; ?>
                </div>
            <?php endif; ?>
            <?php if ($success_msg && empty($errors)): ?>
                <div class="alert success"><?php echo $success_msg; ?></div>
            <?php endif; ?>

            <div class="form-card">
                <h4>Informasi Akun</h4>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <small class="form-text text-muted">Email tidak dapat diubah.</small>
                </div>
            </div>

            <div class="form-card">
                <h4>Ubah Password</h4>
                <p class="form-text text-muted mb-3">Kosongkan jika tidak ingin mengubah password.</p>
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter">
                </div>
                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Ketik ulang password baru">
                </div>
            </div>

            <button type="submit" class="btn-save-profile">Simpan Perubahan</button>
        </form>
    </div>
</main>
    
<?php include 'includes/footer.php'; ?>