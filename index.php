<?php
// index.php

require 'vendor/autoload.php';

use App\Client\PrayerTimeClient;

// --- 1. KONFIGURASI & INPUT ---
$apiKey = getenv('PRAYER_TIME_API_KEY') ?: 'not_required'; 
$error_message = null;
$result_data = null;
$city = 'Jakarta';
$country = 'Indonesia';

// Ambil input dari form jika ada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = htmlspecialchars($_POST['city'] ?? $city);
    $country = htmlspecialchars($_POST['country'] ?? $country);

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
    <title>Pencari Jadwal Sholat</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .header-bg {
            background-color: #0d6efd;
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
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

<header class="header-bg">
    <div class="container text-center">
        <h1>🕌 Pencari Jadwal Sholat</h1>
        <p>Aplikasi REST Client Sederhana</p>
    </div>
</header>

<main class="container">
    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="card p-4 shadow">
                <h5 class="card-title">Cari Lokasi</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label for="city" class="form-label">Kota</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?= $city ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Negara</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?= $country ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cari Jadwal</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <?php if ($result_data): ?>
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
            <?php endif; ?>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>