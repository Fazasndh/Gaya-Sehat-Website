<?php
session_start();
require 'config/database.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = htmlspecialchars(trim($_POST['username_or_email']));
    $password = $_POST['password'];
    
    if (empty($username_or_email)) $errors[] = 'Username atau email wajib diisi.';
    if (empty($password)) $errors[] = 'Password wajib diisi.';
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id_user, username, email, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username_or_email, $username_or_email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard_admin.php");
            } else {
                header("Location: dashboard_user.php");
            }
            exit;
        } else {
            $errors[] = 'Username/email atau password salah.';
        }
    }
}
$registration_success = isset($_GET['status']) && $_GET['status'] === 'registered';


$page_title = 'Login - Gaya Sehat';
require_once 'includes/header_user.php'; 
?>

<main class="container page-content">
    <div class="form-wrapper">
        <form action="login.php" method="POST" class="auth-form-simple">
            <h2>Masuk ke Akun Anda</h2>

            <?php if ($registration_success): ?>
                <div class="alert success">Registrasi berhasil! Silakan login.</div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $error) echo "<p class='m-0'>$error</p>"; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="username_or_email">Username atau Email</label>
                <input type="text" id="username_or_email" name="username_or_email" required value="<?php echo isset($username_or_email) ? htmlspecialchars($username_or_email) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <div class="form-footer-note">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </form>
    </div>
</main>

<?php 
require_once 'includes/footer.php'; 
?>