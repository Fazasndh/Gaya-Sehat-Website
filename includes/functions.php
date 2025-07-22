<?php
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
function create_excerpt($text, $length = 100) {
    // Menghapus tag HTML agar tidak ikut terpotong
    $text = strip_tags($text);
    
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length);
        // Memastikan tidak memotong kata di tengah
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= '...';
    }
    return $text;
}

function require_admin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../dashboard_user.php'); 
        exit;
    }
}
?>