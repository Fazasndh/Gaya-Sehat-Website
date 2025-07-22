<?php 
$page_title = 'Tambah Artikel Baru'; 
require_once 'includes/header.php'; 

// --- PHP LOGIC UNTUK MEMPROSES FORM ---

$errors = [];
$title = '';
$content = '';
$id_category = '';
$status = 'draft';

// Ambil daftar kategori dari database untuk ditampilkan di dropdown
try {
    $stmt_categories = $pdo->query("SELECT id_category, name FROM categories ORDER BY name ASC");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Gagal memuat kategori: " . $e->getMessage();
    $categories = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $id_category = $_POST['id_category'];
    $status = $_POST['status'];
    $id_user = $_SESSION['user_id']; 
    $image_name = '';

    // Validasi dasar
    if (empty($title)) {
        $errors[] = "Judul artikel tidak boleh kosong.";
    }
    if (empty($content)) {
        $errors[] = "Konten artikel tidak boleh kosong.";
    }
    if (empty($id_category)) {
        $errors[] = "Kategori harus dipilih.";
    }

    // Proses upload gambar (jika ada file yang diunggah)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_type = $_FILES['image']['type'];
        $file_name_parts = explode('.', $file_name);
        $file_ext = strtolower(end($file_name_parts));

        // Buat nama file yang unik untuk menghindari penimpaan file
        $image_name = time() . '_' . $file_name;
        $upload_dir = '../assets/uploads/';
        $dest_path = $upload_dir . $image_name;

        // Validasi tipe file
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_exts)) {
            // Pindahkan file dari temporary ke folder uploads
            if(!move_uploaded_file($file_tmp_path, $dest_path)) {
                $errors[] = 'Gagal memindahkan file gambar.';
            }
        } else {
            $errors[] = 'Format file gambar tidak valid. Hanya JPG, JPEG, PNG, GIF yang diizinkan.';
        }
    } else {
        $errors[] = 'Gambar utama artikel wajib diunggah.';
    }

    // Jika tidak ada error, masukkan data ke database
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO articles (title, content, id_category, id_user, status, image) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $id_category, $id_user, $status, $image_name]);

            // Redirect ke halaman manajemen artikel dengan pesan sukses
            header("Location: manage_articles.php?status=added");
            exit;

        } catch (PDOException $e) {
            $errors[] = "Gagal menyimpan artikel ke database: " . $e->getMessage();
        }
    }
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Formulir Artikel Baru</h5>
        <a href="manage_articles.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="add_article.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="title" class="form-label">Judul Artikel</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">Konten</label>
                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="id_category" class="form-label">Kategori</label>
                    <select class="form-select" id="id_category" name="id_category" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id_category']; ?>" <?php if ($id_category == $category['id_category']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="draft" <?php if ($status == 'draft') echo 'selected'; ?>>Draft</option>
                        <option value="published" <?php if ($status == 'published') echo 'selected'; ?>>Published</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Gambar Utama Artikel</label>
                <input class="form-control" type="file" id="image" name="image" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Artikel</button>
        </form>
    </div>
</div>

<?php 
require_once 'includes/footer.php'; 
?>