<?php
// hasil.php

require 'vendor/autoload.php';

use App\Client\PrayerTimeClient;

// --- 1. KONFIGURASI & INPUT ---
$apiKey = getenv('PRAYER_TIME_API_KEY') ?: 'not_required'; 
$error_message = null;
$result_data = null;

// Ambil parameter dari URL
$city = htmlspecialchars($_GET['city'] ?? '');
$country = htmlspecialchars($_GET['country'] ?? '');

// Jika tidak ada parameter, redirect ke halaman cari lokasi
if (empty($city) || empty($country)) {
    header("Location: cari_lokasi.php");
    exit;
}

try {
    // Inisialisasi Klien (Menggunakan kelas buatan Developer 1)
    $client = new PrayerTimeClient($apiKey);

    // Ambil Data
    $result = $client->getDailyTimesByCity($city, $country);

    if ($result['success']) {
        $result_data = $result;
    } else {
        $error_message = $result['message'];
    }

} catch (\InvalidArgumentException $e) {
    $error_message = "❌ ERROR KONFIGURASI: " . $e->getMessage();
} catch (\Exception $e) {
    $error_message = "❌ ERROR UMUM: " . $e->getMessage();
}

// Map jadwal sholat ke label yang lebih mudah dibaca
$prayer_map = [
    'Fajr' => ['label' => 'Subuh', 'icon' => 'sun'],
    'Sunrise' => ['label' => 'Terbit', 'icon' => 'sunrise'],
    'Dhuhr' => ['label' => 'Dzuhur', 'icon' => 'cloud-sun'],
    'Asr' => ['label' => 'Ashar', 'icon' => 'cloud-sun-fill'],
    'Maghrib' => ['label' => 'Maghrib', 'icon' => 'sunset'],
    'Isha' => ['label' => 'Isya', 'icon' => 'moon-stars'],
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Jadwal Sholat - Pencari Jadwal Sholat</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: #0d6efd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand,
        .nav-link {
            color: white !important;
        }
        
        .nav-link:hover {
            color: #ffc107 !important;
        }
        
        .prayer-card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .prayer-card:hover {
            transform: translateY(-5px);
        }
        .timing {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .imsak-style {
            background-color: #ffc107; /* Yellow */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-house-door-fill me-2"></i>Pencari Jadwal Sholat
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house-door me-1"></i>Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cari_lokasi.php">
                        <i class="bi bi-search me-1"></i>Cari Lokasi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container mt-4">
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?= $error_message ?>
        </div>
        <div class="text-center mt-4">
            <a href="cari_lokasi.php" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Cari Lokasi
            </a>
        </div>
    <?php elseif ($result_data): ?>
        <div class="card p-4 shadow mb-4">
            <h4 class="card-title text-primary">Jadwal Sholat Harian</h4>
            <p class="mb-1"><strong>Lokasi:</strong> <?= $result_data['city'] ?>, <?= $result_data['country'] ?></p>
            <p class="mb-0"><strong>Tanggal:</strong> <?= $result_data['date'] ?></p>
        </div>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            
            <?php if (isset($result_data['timings']['Imsak'])): ?>
                <div class="col">
                    <div class="card prayer-card text-center imsak-style">
                        <div class="card-body">
                            <i class="bi bi-clock-fill display-6"></i>
                            <h5 class="card-title mt-2">Imsak</h5>
                            <div class="timing"><?= $result_data['timings']['Imsak'] ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($prayer_map as $key => $details): ?>
                <?php if (isset($result_data['timings'][$key])): ?>
                    <div class="col">
                        <div class="card prayer-card text-center">
                            <div class="card-body">
                                <i class="bi bi-<?= $details['icon'] ?> display-6"></i>
                                <h5 class="card-title mt-2"><?= $details['label'] ?></h5>
                                <div class="timing"><?= $result_data['timings'][$key] ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="cari_lokasi.php" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Cari Lokasi Lain
            </a>
        </div>
    <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

