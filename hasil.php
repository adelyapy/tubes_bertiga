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
$date = htmlspecialchars($_GET['date'] ?? date('Y-m-d')); // Tambahkan parameter tanggal

// Jika tidak ada parameter, redirect ke halaman cari lokasi
if (empty($city) || empty($country)) {
    header("Location: cari_lokasi.php");
    exit;
}

try {
    // Inisialisasi Klien (Menggunakan kelas buatan Developer 1)
    $client = new PrayerTimeClient($apiKey);

    // Ambil Data dengan parameter tanggal
    $result = $client->getDailyTimesByCity($city, $country, $date);

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
        
        /* Style untuk jam realtime */
        .realtime-clock {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        
        .clock-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .clock-time {
            font-size: 2.5rem;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin-bottom: 5px;
        }
        .clock-date {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .clock-text {
            text-align: center;
            flex: 1;
            min-width: 200px;
        }
        
        /* Style untuk jam analog */
        .analog-clock {
            width: 150px;
            height: 150px;
            border: 8px solid white;
            border-radius: 50%;
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        
        .clock-face {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        /* Jarum jam */
        .hand {
            position: absolute;
            transform-origin: bottom center;
            background: white;
            border-radius: 5px;
            left: 50%;
            bottom: 50%;
        }
        
        .hour-hand {
            width: 6px;
            height: 40px;
            margin-left: -3px;
            transform: translateY(10px);
            z-index: 3;
        }
        
        .minute-hand {
            width: 4px;
            height: 55px;
            margin-left: -2px;
            transform: translateY(5px);
            z-index: 2;
        }
        
        .second-hand {
            width: 2px;
            height: 60px;
            margin-left: -1px;
            background: #ffc107;
            transform: translateY(0);
            z-index: 1;
        }
        
        /* Titik tengah */
        .clock-center {
            position: absolute;
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 4;
        }
        
        /* Ikon matahari/bulan */
        .time-icon {
            font-size: 3rem;
            animation: rotate 20s linear infinite;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
        }
        
        .time-icon.sun {
            color: #ffd700;
            animation: rotate 20s linear infinite;
        }
        
        .time-icon.moon {
            color: #e0e0e0;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        @media (max-width: 768px) {
            .clock-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .analog-clock {
                width: 120px;
                height: 120px;
            }
            
            .hour-hand {
                height: 30px;
            }
            
            .minute-hand {
                height: 40px;
            }
            
            .second-hand {
                height: 45px;
            }
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
        <!-- Jam Realtime -->
        <div class="realtime-clock">
            <div class="clock-container">
                <!-- Ikon Matahari/Bulan -->
                <div id="time-icon-container">
                    <i class="bi bi-sun-fill time-icon sun" id="time-icon" style="display: none;"></i>
                    <i class="bi bi-moon-stars-fill time-icon moon" id="time-icon-moon" style="display: none;"></i>
                </div>
                
                <!-- Jam Analog -->
                <div class="analog-clock">
                    <div class="clock-face">
                        <div class="hand hour-hand" id="hour-hand"></div>
                        <div class="hand minute-hand" id="minute-hand"></div>
                        <div class="hand second-hand" id="second-hand"></div>
                        <div class="clock-center"></div>
                    </div>
                </div>
                
                <!-- Jam Digital -->
                <div class="clock-text">
                    <div class="clock-time" id="realtime-clock">--:--:--</div>
                    <div class="clock-date" id="realtime-date">-- -- ----</div>
                </div>
            </div>
        </div>
        
        <div class="card p-4 shadow mb-4">
            <h4 class="card-title text-primary">Jadwal Sholat Harian</h4>
            <p class="mb-1"><strong>Lokasi:</strong> <?= $result_data['city'] ?>, <?= $result_data['country'] ?></p>
            <p class="mb-3"><strong>Tanggal:</strong> <?= $result_data['date'] ?></p>
            
            <!-- Form untuk mengubah tanggal -->
            <form method="GET" action="hasil.php" class="mt-3">
                <input type="hidden" name="city" value="<?= htmlspecialchars($city) ?>">
                <input type="hidden" name="country" value="<?= htmlspecialchars($country) ?>">
                <div class="row g-2">
                    <div class="col-md-8">
                        <label for="date" class="form-label">Pilih Tanggal Lain:</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-calendar-check me-2"></i>Ubah Tanggal
                        </button>
                    </div>
                </div>
            </form>
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

<script>
// Fungsi untuk update jam realtime dan analog
function updateRealtimeClock() {
    const now = new Date();
    
    // Format waktu: HH:MM:SS
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const seconds = now.getSeconds();
    
    const hoursStr = String(hours).padStart(2, '0');
    const minutesStr = String(minutes).padStart(2, '0');
    const secondsStr = String(seconds).padStart(2, '0');
    const timeString = `${hoursStr}:${minutesStr}:${secondsStr}`;
    
    // Format tanggal: DD MMMM YYYY (dalam bahasa Indonesia)
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const dayName = days[now.getDay()];
    const day = String(now.getDate()).padStart(2, '0');
    const month = months[now.getMonth()];
    const year = now.getFullYear();
    const dateString = `${dayName}, ${day} ${month} ${year}`;
    
    // Update jam digital
    document.getElementById('realtime-clock').textContent = timeString;
    document.getElementById('realtime-date').textContent = dateString;
    
    // Update jam analog
    const hourHand = document.getElementById('hour-hand');
    const minuteHand = document.getElementById('minute-hand');
    const secondHand = document.getElementById('second-hand');
    
    // Hitung sudut untuk setiap jarum
    // Jam: 12 jam = 360 derajat, jadi 1 jam = 30 derajat, ditambah menit/60 * 30
    const hourAngle = (hours % 12) * 30 + (minutes / 60) * 30;
    // Menit: 60 menit = 360 derajat, jadi 1 menit = 6 derajat
    const minuteAngle = minutes * 6 + (seconds / 60) * 6;
    // Detik: 60 detik = 360 derajat, jadi 1 detik = 6 derajat
    const secondAngle = seconds * 6;
    
    hourHand.style.transform = `translateY(10px) rotate(${hourAngle}deg)`;
    minuteHand.style.transform = `translateY(5px) rotate(${minuteAngle}deg)`;
    secondHand.style.transform = `translateY(0) rotate(${secondAngle}deg)`;
    
    // Tentukan siang atau malam (siang: 6:00 - 18:00)
    const sunIcon = document.getElementById('time-icon');
    const moonIcon = document.getElementById('time-icon-moon');
    
    if (hours >= 6 && hours < 18) {
        // Siang - tampilkan matahari
        sunIcon.style.display = 'block';
        moonIcon.style.display = 'none';
    } else {
        // Malam - tampilkan bulan
        sunIcon.style.display = 'none';
        moonIcon.style.display = 'block';
    }
}

// Update jam setiap detik
setInterval(updateRealtimeClock, 1000);

// Panggil fungsi pertama kali untuk langsung menampilkan jam
updateRealtimeClock();
</script>
</body>
</html>

