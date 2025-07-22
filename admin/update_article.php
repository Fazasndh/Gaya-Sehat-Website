<?php 
// --- SETUP AWAL ---
$page_title = 'Edit Artikel'; 
require_once 'includes/header.php'; 

// 1. Validasi ID Artikel dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Jika tidak ada ID atau ID tidak valid, kembalikan ke halaman manajemen
    header("Location: manage_articles.php?status=invalid_id");
    exit;
}
$id_article = $_GET['id'];

$errors = [];

// Ambil daftar kategori untuk dropdown
try {
    $stmt_categories = $pdo->query("SELECT id_category, name FROM categories ORDER BY name ASC");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Gagal memuat kategori: " . $e->getMessage();
    $categories = [];
}


// --- LOGIKA UTAMA: PROSES POST ATAU GET ---

// JIKA FORM DI-SUBMIT (METHOD POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form yang disubmit
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $id_category = $_POST['id_category'];
    $status = $_POST['status'];
    $old_image = $_POST['old_image']; 

    // Validasi
    if (empty($title)) $errors[] = "Judul tidak boleh kosong.";
    if (empty($content)) $errors[] = "Konten tidak boleh kosong.";
    if (empty($id_category)) $errors[] = "Kategori harus dipilih.";

    $image_name = $old_image; 

    // Logika upload gambar BARU (jika admin mengunggah gambar baru)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Hapus gambar lama jika ada gambar baru yang diunggah
        if (!empty($old_image) && file_exists('../assets/uploads/' . $old_image)) {
            unlink('../assets/uploads/' . $old_image);
        }

        // Proses gambar baru
        $file_tmp_path = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $image_name = time() . '_' . $file_name;
        $dest_path = '../assets/uploads/' . $image_name;
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_exts)) {
            if(!move_uploaded_file($file_tmp_path, $dest_path)) {
                $errors[] = 'Gagal memindahkan file gambar baru.';
            }
        } else {
            $errors[] = 'Format file gambar baru tidak valid.';
            $image_name = $old_image; 
        }
    }

    // Jika tidak ada error, UPDATE data di database
    if (empty($errors)) {
        try {
            $sql = "UPDATE articles SET title = ?, content = ?, id_category = ?, status = ?, image = ? 
                    WHERE id_article = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $id_category, $status, $image_name, $id_article]);

            header("Location: manage_articles.php?status=updated");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Gagal memperbarui artikel: " . $e->getMessage();
        }
    }

// JIKA BUKAN METHOD POST (SAAT HALAMAN PERTAMA KALI DIBUKA)
} else {
    try {
        // Ambil data artikel yang ada dari database untuk ditampilkan di form
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id_article = ?");
        $stmt->execute([$id_article]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$article) {
            // Jika artikel dengan ID tersebut tidak ditemukan
            header("Location: manage_articles.php?status=not_found");
            exit;
        }
        
        // Isi variabel dari data yang ada di database
        $title = $article['title'];
        $content = $article['content'];
        $id_category = $article['id_category'];
        $status = $article['status'];
        $old_image = $article['image'];

    } catch (PDOException $e) {
        $errors[] = "Gagal mengambil data artikel: " . $e->getMessage();
    }
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Edit Artikel</h5>
        <a href="manage_articles.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="card-body">
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($old_image); ?>">

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
                <label for="image" class="form-label">Ganti Gambar Utama (Opsional)</label>
                <br>
                <?php if (!empty($old_image)): ?>
                    <img src="../assets/uploads/<?php echo htmlspecialchars($old_image); ?>" alt="Current Image" width="150" class="mb-2 img-thumbnail">
                <?php endif; ?>
                <input class="form-control" type="file" id="image" name="image">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php 
require_once 'includes/footer.php'; 
?>