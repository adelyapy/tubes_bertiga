<?php
// index.php

require 'vendor/autoload.php';

use App\Client\PrayerTimeClient;

// --- KONFIGURASI ---
// API Key diambil dari Environment Variable (sesuai best practice)
// Dalam konteks ini, kita set 'not_required' karena API Aladhan tidak memerlukan key.
$apiKey = getenv('PRAYER_TIME_API_KEY') ?: 'not_required'; 
$city = $argv[1] ?? 'Surabaya'; // Ambil kota dari CLI, default Surabaya
$country = $argv[2] ?? 'Indonesia'; // Ambil negara dari CLI, default Indonesia

echo "========================================================\n";
echo "🕌 Aplikasi Pencari Jadwal Sholat \n";
echo "========================================================\n";
echo "Mencari Jadwal: $city, $country\n";
echo "--------------------------------------------------------\n";

try {
    // 1. Inisialisasi Klien
    $client = new PrayerTimeClient($apiKey);

    // 2. Ambil Data
    $result = $client->getDailyTimesByCity($city, $country);

    if ($result['success']) {
        
        // 3. Tampilkan Data
        $timings = $result['timings'];
        
        echo "Tanggal: {$result['date']}\n";
        echo "Kota: {$result['city']}, {$result['country']}\n";
        echo "--------------------------------------------------------\n";
        
        // Tampilan dalam bentuk tabel sederhana CLI
        printf("%-10s | %-10s\n", "SHOLAT", "WAKTU");
        echo str_repeat("-", 23) . "\n";
        
        // Filter dan tampilkan jadwal utama saja
        $mainTimings = [
            'Imsak', 'Fajr' => 'Subuh', 'Sunrise' => 'Terbit', 
            'Dhuhr' => 'Dzuhur', 'Asr' => 'Ashar', 
            'Maghrib', 'Isha' => 'Isya'
        ];

        foreach ($mainTimings as $key => $label) {
            // Menangani kasus saat key dan label sama (cth: Imsak, Maghrib)
            $apiTimeKey = is_string($key) ? $key : $label;
            $displayLabel = is_string($key) ? $label : $key; 

            if (isset($timings[$apiTimeKey])) {
                printf("%-10s | %-10s\n", $displayLabel, $timings[$apiTimeKey]);
            }
        }
        
    } else {
        echo "⚠️ GAGAL MENGAMBIL DATA:\n";
        echo $result['message'] . "\n";
    }

} catch (\InvalidArgumentException $e) {
    echo "❌ ERROR KONFIGURASI: " . $e->getMessage() . "\n";
    echo "Pastikan API Key sudah benar.\n";
} catch (\Exception $e) {
    echo "❌ ERROR UMUM: " . $e->getMessage() . "\n";
}

echo "========================================================\n";