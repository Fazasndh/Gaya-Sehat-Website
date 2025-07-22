<?php
require 'config/database.php';
session_start(); 
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. input awal
    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // 2. Validasi
    if (empty($username)) $errors[] = 'Username wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if (strlen($password) < 6) { 
        $errors[] = 'Password harus minimal 6 karakter.';
    }
    if ($password !== $password_confirm) $errors[] = 'Konfirmasi password tidak cocok.';

    // 3. mengecek apakah email atau username sudah ada
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username atau email sudah terdaftar.';
        }
    }

    // 4. memasukkan data ke db
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            header("Location: login.php?status=registered");
            exit;
        } else {
            $errors[] = 'Registrasi gagal. Silakan coba lagi.';
        }
    }
}


$page_title = 'Register - Gaya Sehat';
require_once 'includes/header_user.php'; 
?>

<main class="container page-content">
    <div class="form-wrapper">
        <form action="register.php" method="POST" class="auth-form-simple">
            <h2>Buat Akun Baru</h2>
            <p class="form-subheading" style="margin-bottom: 2rem;">Bergabunglah bersama kami untuk memulai hidup sehatmu!</p>

            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $error): ?>
                        <p class="m-0"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Konfirmasi Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>

            <button type="submit">Register</button>

            <div class="form-footer-note">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </form>
    </div>
</main>

<?php 
require_once 'includes/footer.php'; 
?>