<?php
session_start();

// Inisialisasi variabel
$calculation_done = false;
$bmi_value = 0;
$bmi_category = '';
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $height = isset($_POST['height']) ? (float)$_POST['height'] : 0;
    $weight = isset($_POST['weight']) ? (float)$_POST['weight'] : 0;
    
    if ($weight > 0 && $height > 0) {
        $calculation_done = true; // Tandai bahwa kalkulasi sudah dilakukan
        $height_in_meter = $height / 100;
        $bmi_value = round($weight / ($height_in_meter * $height_in_meter), 1);
        
        if ($bmi_value < 18.5) {
            $bmi_category = 'Underweight';
        } elseif ($bmi_value < 25) {
            $bmi_category = 'Normal';
        } elseif ($bmi_value < 30) {
            $bmi_category = 'Overweight';
        } else {
            $bmi_category = 'Obese';
        }

        // Jika user login, simpan riwayat
        if (isset($_SESSION['user_id'])) {
            require 'config/database.php'; 
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO bmi_history (id_user, height, weight, bmi_value, category) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $_SESSION['user_id'], $height, $weight, $bmi_value, $bmi_category
                ]);
            } catch (PDOException $e) {
                $error_message = "Database Error: " . $e->getMessage();
            }
        }
    } else {
        $error_message = "Harap masukkan berat dan tinggi badan yang valid.";
    }
}

// Menyiapkan judul halaman sebelum memanggil header
$page_title = 'Kalkulator BMI';
require_once 'includes/header_user.php';
?>

<div class="calculator-page-header">
    <div class="container">
        <h2>Kalkulator Body Mass Index (BMI)</h2>
        <p>Cek status berat badan Anda untuk langkah awal menuju hidup yang lebih sehat.</p>
    </div>
</div>

<main class="container">
    <div class="calculator-wrapper">
        
        <div class="calculator-form-card">
            <h3>Masukkan Data Anda</h3>
            <form action="bmi_calculator.php" method="POST">
                <?php if ($error_message): ?>
                    <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="weight">Berat Badan (kg)</label>
                    <input type="number" step="0.1" id="weight" name="weight" placeholder="Contoh: 65.5" required>
                </div>
                <div class="form-group">
                    <label for="height">Tinggi Badan (cm)</label>
                    <input type="number" step="0.1" id="height" name="height" placeholder="Contoh: 170" required>
                </div>
                <button type="submit" class="btn-calculate">Hitung BMI Sekarang</button>
            </form>
        </div>

        <div class="calculator-info-card">
            <?php if ($calculation_done === false): // Tampilan Awal SEBELUM Menghitung ?>
                <h4>Apa itu BMI?</h4>
                <p>Indeks Massa Tubuh (IMT atau BMI) adalah angka yang menjadi standar untuk menilai apakah berat badan Anda tergolong normal, kurus, atau gemuk.</p>
                
                <h4>Kategori BMI</h4>
                <ul class="bmi-category-list">
                    <li><span class="dot underweight"></span> Kurang dari 18.5: Berat Badan Kurang</li>
                    <li><span class="dot normal"></span> 18.5 - 24.9: Normal (Ideal)</li>
                    <li><span class="dot overweight"></span> 25.0 - 29.9: Berat Badan Berlebih</li>
                    <li><span class="dot obese"></span> 30.0 atau lebih: Kegemukan (Obesitas)</li>
                </ul>

            <?php else: // Tampilan Hasil SETELAH Menghitung ?>
                
                <div class="result-display category-<?php echo strtolower($bmi_category); ?>">
                    <p>Hasil BMI Anda:</p>
                    <div class="result-bmi-value"><?php echo str_replace('.', ',', htmlspecialchars($bmi_value)); ?></div>
                    <div class="result-bmi-category"><?php echo htmlspecialchars($bmi_category); ?></div>
                </div>
                <div class="result-interpretation">
                    <?php
                        // Memberikan interpretasi berdasarkan kategori
                        switch ($bmi_category) {
                            case 'Underweight':
                                echo "Hasil ini menunjukkan berat badan Anda mungkin kurang dari ideal. Pertimbangkan untuk menambah asupan nutrisi seimbang.";
                                break;
                            case 'Normal':
                                echo "Selamat! Berat badan Anda berada dalam rentang ideal. Terus pertahankan gaya hidup sehat Anda.";
                                break;
                            case 'Overweight':
                                echo "Anda memiliki berat badan berlebih. Mengatur pola makan dan meningkatkan aktivitas fisik adalah langkah awal yang baik.";
                                break;
                            case 'Obese':
                                echo "Anda berada dalam kategori obesitas. Disarankan untuk berkonsultasi dengan ahli gizi atau dokter untuk program kesehatan yang tepat.";
                                break;
                        }
                    ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</main>

<?php 
require_once 'includes/footer.php'; 
?>