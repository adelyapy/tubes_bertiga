<?php
namespace App\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PrayerTimeClient {
    private $client;
    private $apiKey;
    // Menggunakan API Aladhan sebagai contoh
    private $baseUrl = 'https://api.aladhan.com/v1/'; 

    /**
     * @param string $apiKey API Key. Jika tidak diperlukan API Key (seperti Aladhan), bisa dilewatkan string kosong.
     */
    public function __construct(string $apiKey) {
        // TC 3: API Key Tidak Boleh Kosong (Pengamanan)
        // Throw exception jika kosong (empty string)
        if (trim($apiKey) === '') {
            throw new \InvalidArgumentException("API Key harus disediakan atau set 'not_required'.");
        }
        $this->apiKey = $apiKey;
        $this->client = new Client(['base_uri' => $this->baseUrl]);
    }

    /**
     * Mengambil jadwal sholat untuk tanggal tertentu di kota tertentu.
     * @param string $city Nama kota
     * @param string $country Nama negara
     * @param string|null $date Tanggal dalam format Y-m-d (contoh: 2025-12-01). Jika null, gunakan tanggal hari ini.
     */
    public function getDailyTimesByCity(string $city, string $country, ?string $date = null): array {
        try {
            // Jika tanggal tidak diberikan, gunakan tanggal hari ini
            if ($date === null) {
                $date = date('Y-m-d');
            }
            
            // Parse tanggal untuk mendapatkan bulan dan tahun
            $dateParts = explode('-', $date);
            $year = $dateParts[0] ?? date('Y');
            $month = $dateParts[1] ?? date('m');
            $day = $dateParts[2] ?? date('d');
            
            $response = $this->client->request('GET', 'calendarByCity', [
                'query' => [
                    'city' => $city,
                    'country' => $country,
                    'month' => $month,
                    'year' => $year,
                    'method' => 8, // Metode perhitungan
                ]
            ]);

            // TC 4: Response Code Harus 200
            if ($response->getStatusCode() !== 200) {
                return [
                    'success' => false, 
                    'message' => 'Gagal mengambil data, Kode: ' . $response->getStatusCode()
                ];
            }

            $data = json_decode($response->getBody()->getContents(), true);
            
            // TC 5: Valid JSON Response (Struktur Data)
            if (!isset($data['data'])) {
                return [
                    'success' => false, 
                    'message' => 'Struktur data JSON tidak valid: Kunci data hilang.'
                ];
            }
            
            // Cari data untuk tanggal yang diminta
            $selectedDateData = null;
            foreach ($data['data'] as $dayData) {
                if (isset($dayData['date']['gregorian']['date']) && 
                    $dayData['date']['gregorian']['date'] === $date) {
                    $selectedDateData = $dayData;
                    break;
                }
            }
            
            // Jika tidak ditemukan, coba dengan format lain atau gunakan index berdasarkan hari
            if ($selectedDateData === null) {
                // Coba mencari berdasarkan readable date atau gunakan index
                $dayIndex = (int)$day - 1;
                if (isset($data['data'][$dayIndex])) {
                    $selectedDateData = $data['data'][$dayIndex];
                } else {
                    return ['success' => false, 'message' => 'Jadwal sholat untuk tanggal yang dipilih tidak ditemukan.'];
                }
            }
            
            if (empty($selectedDateData['timings'])) {
                 return ['success' => false, 'message' => 'Jadwal sholat untuk tanggal yang dipilih tidak ditemukan.'];
            }

            return [
                'success' => true,
                'city' => $city,
                'country' => $country,
                'date' => $selectedDateData['date']['readable'],
                'timings' => $selectedDateData['timings']
            ];

        } catch (RequestException $e) {
            return [
                'success' => false, 
                'message' => 'Request Error: ' . $e->getMessage()
            ];
        }
    }
}

