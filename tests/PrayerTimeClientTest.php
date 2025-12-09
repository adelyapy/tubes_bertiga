<?php
use PHPUnit\Framework\TestCase;
// PERBAIKAN PENTING: Huruf 'C' pada Client harus besar sesuai nama folder
use App\Client\PrayerTimeClient; 
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class PrayerTimeClientTest extends TestCase {
    
    // --- Test Case Dasar (TC 1 & TC 2) ---
    // TC 1: File Exist & TC 2: Valid Syntax (Dikerjakan otomatis oleh PHPUnit)
    public function testClientCanBeInstantiated() {
        // Menggunakan key dummy 'not_required' untuk testing
        $client = new PrayerTimeClient('not_required'); 
        
        // Memastikan objek yang terbentuk adalah instance dari PrayerTimeClient
        $this->assertInstanceOf(PrayerTimeClient::class, $client);
    }

    // --- Test Case Validasi (TC 3) ---
    public function testThrowsExceptionIfApiKeyIsEmpty() {
        // TC 3: API Key Tidak Boleh Kosong (Menguji pengamanan Secret/Key)
        // Kita berharap kode akan melempar error (Exception) jika key kosong
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("API Key harus disediakan atau set 'not_required'.");
        
        // Menjalankan inisialisasi dengan string kosong untuk memicu error
        new PrayerTimeClient('');
    }
    
    // --- Test Case Fungsional (TC 4, TC 5, TC 6) ---
    public function testGetTimesReturnsValidDataStructureAndStatus() {
        // Untuk testing fungsionalitas, kita menggunakan live API
        $client = new PrayerTimeClient('not_required'); 
        
        // Mengambil data untuk kota Bandung
        $result = $client->getDailyTimesByCity('Bandung', 'Indonesia');

        // TC 4: Response Code Harus 200 (diwakili oleh 'success' => true)
        // Jika API sedang down/bermasalah, test ini akan memberi tahu kita
        $this->assertTrue($result['success'], "Panggilan API harus sukses. Pesan Error: " . ($result['message'] ?? ''));
        
        // TC 5: Valid JSON Response (Struktur Data)
        // Memastikan hasil memiliki kunci 'timings'
        $this->assertArrayHasKey('timings', $result, "Response harus memiliki kunci 'timings'");
        
        // TC 6: Validasi Data Spesifik (Cek format waktu di kunci Fajr)
        // Memastikan data timings memiliki jadwal 'Fajr'
        $this->assertArrayHasKey('Fajr', $result['timings'], "Data harus memiliki jadwal Fajr");
        
        // Memastikan format waktu adalah HH:MM (contoh: 04:30)
        $this->assertMatchesRegularExpression('/\d{2}:\d{2}/', $result['timings']['Fajr'], "Waktu Fajr harus format HH:MM");
    }

    // --- Test Case Tambahan: Skenario Gagal ---
    public function testReturnsFailureOnInvalidCity() {
        $client = new PrayerTimeClient('not_required'); 
        
        // Menguji dengan nama kota yang fiktif
        $result = $client->getDailyTimesByCity('Kota_Fiktif_2025', 'Noland');

        // Kita harapkan API mengembalikan status gagal (success=false)
        $this->assertFalse($result['success'], "Panggilan ke kota fiktif seharusnya gagal (return false).");
        
        // Memastikan pesan error yang sesuai muncul
        $this->assertStringContainsString('Request Error', $result['message'], "Pesan error tidak sesuai.");
    }

    // --- Test Case Baru 1: Validasi Semua Waktu Sholat Wajib ---
    public function testReturnsAllRequiredPrayerTimes() {
        $client = new PrayerTimeClient('not_required'); 
        
        // Mengambil data untuk kota Jakarta
        $result = $client->getDailyTimesByCity('Jakarta', 'Indonesia');

        // Memastikan response sukses
        $this->assertTrue($result['success'], "Panggilan API harus sukses.");
        
        // Memastikan timings ada
        $this->assertArrayHasKey('timings', $result, "Response harus memiliki kunci 'timings'");
        
        // Memastikan semua waktu sholat wajib ada (5 waktu)
        $requiredPrayers = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
        foreach ($requiredPrayers as $prayer) {
            $this->assertArrayHasKey($prayer, $result['timings'], "Data harus memiliki jadwal {$prayer}");
        }
        
        // Memastikan format waktu semua sholat valid (HH:MM)
        foreach ($requiredPrayers as $prayer) {
            $this->assertMatchesRegularExpression(
                '/\d{2}:\d{2}/', 
                $result['timings'][$prayer], 
                "Waktu {$prayer} harus format HH:MM"
            );
        }
    }

    // --- Test Case Baru 2: Validasi Struktur Response Lengkap ---
    public function testResponseHasCompleteDataStructure() {
        $client = new PrayerTimeClient('not_required'); 
        
        // Mengambil data untuk kota Surabaya
        $result = $client->getDailyTimesByCity('Surabaya', 'Indonesia');

        // Memastikan response sukses
        $this->assertTrue($result['success'], "Panggilan API harus sukses.");
        
        // Memastikan semua field yang diperlukan ada
        $requiredFields = ['success', 'city', 'country', 'date', 'timings'];
        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey($field, $result, "Response harus memiliki field '{$field}'");
        }
        
        // Memastikan nilai field sesuai dengan input
        $this->assertEquals('Surabaya', $result['city'], "Field city harus sesuai dengan input");
        $this->assertEquals('Indonesia', $result['country'], "Field country harus sesuai dengan input");
        
        // Memastikan date tidak kosong
        $this->assertNotEmpty($result['date'], "Field date tidak boleh kosong");
        
        // Memastikan timings adalah array dan tidak kosong
        $this->assertIsArray($result['timings'], "Field timings harus berupa array");
        $this->assertNotEmpty($result['timings'], "Field timings tidak boleh kosong");
    }
}