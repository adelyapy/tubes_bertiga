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
     * Mengambil jadwal sholat hari ini untuk kota tertentu.
     */
    public function getDailyTimesByCity(string $city, string $country): array {
        try {
            $response = $this->client->request('GET', 'calendarByCity', [
                'query' => [
                    'city' => $city,
                    'country' => $country,
                    'month' => date('m'),
                    'year' => date('Y'),
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
            
            // Mengambil data jadwal untuk hari ini (asumsi data[0] adalah hari ini)
            if (empty($data['data'][0]['timings'])) {
                 return ['success' => false, 'message' => 'Jadwal sholat untuk hari ini tidak ditemukan.'];
            }

            return [
                'success' => true,
                'city' => $city,
                'country' => $country,
                'date' => $data['data'][0]['date']['readable'],
                'timings' => $data['data'][0]['timings']
            ];

        } catch (RequestException $e) {
            return [
                'success' => false, 
                'message' => 'Request Error: ' . $e->getMessage()
            ];
        }
    }
}

