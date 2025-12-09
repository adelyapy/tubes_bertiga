<?php
use PHPUnit\Framework\TestCase;
// PERBAIKAN PENTING: Huruf 'C' pada Client harus besar sesuai nama folder
use App\Client\PrayerTimeClient; 
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class PrayerTimeClientTest extends TestCase {
    
    // --- Test Case Dasar (TC 1 & TC 2) ---
    public function testClientCanBeInstantiated() {
        // Menggunakan key dummy 'not_required' untuk testing
        $client = new PrayerTimeClient('not_required'); 
        
        // Memastikan objek yang terbentuk adalah instance dari PrayerTimeClient
        $this->assertInstanceOf(PrayerTimeClient::class, $client);
    }

    // --- Test Case Validasi (TC 3) ---
    public function testThrowsExceptionIfApiKeyIsEmpty() {
        // TC 3: API Key Tidak Boleh Kosong (Menguji pengamanan Secret/Key)
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("API Key harus disediakan atau set 'not_required'.");
        
        // Menjalankan inisialisasi dengan string kosong untuk memicu error
        new PrayerTimeClient('');
    }
    
    // --- Test Case Fungsional (TC 4, TC 5, TC 6) ---
    public function testGetTimesReturnsValidDataStructureAndStatus() {
        $client = new PrayerTimeClient('not_required'); 
        
        // Mengambil data untuk kota Bandung
        $result = $client->getDailyTimesByCity('Bandung', 'Indonesia');

        // TC 4: Response Code Harus 200 (diwakili oleh 'success' => true)
        $this->assertTrue($result['success'], "Panggilan API harus sukses. Pesan Error: " . ($result['message'] ?? ''));
        
        // TC 5: Valid JSON Response (Struktur Data)
        $this->assertArrayHasKey('timings', $result, "Response harus memiliki kunci 'timings'");
        
        // TC 6: Validasi Data Spesifik (Cek format waktu di kunci Fajr)
        $this->assertArrayHasKey('Fajr', $result['timings'], "Data harus memiliki jadwal Fajr");
        
        // Memastikan format waktu adalah HH:MM
        $this->assertMatchesRegularExpression('/\d{2}:\d{2}/', $result['timings']['Fajr'], "Waktu Fajr harus format HH:MM");
    }

    // --- Test Case Tambahan: Skenario Gagal (TC 7)---
    public function testReturnsFailureOnInvalidCity() {
        $client = new PrayerTimeClient('not_required'); 
        
        // Menguji dengan nama kota yang fiktif
        $result = $client->getDailyTimesByCity('Kota_Fiktif_2025', 'Noland');

        // Kita harapkan API mengembalikan status gagal (success=false)
        $this->assertFalse($result['success'], "Panggilan ke kota fiktif seharusnya gagal (return false).");
        $this->assertStringContainsString('Request Error', $result['message'], "Pesan error tidak sesuai.");
    }
    
    // --- Test Case Pengujian Case-Insensitivity (TC 8)---
    public function testCanHandleLowerCaseInput() {
        $client = new PrayerTimeClient('not_required');
        
        // Menguji dengan input yang sengaja huruf kecil
        $result = $client->getDailyTimesByCity('surabaya', 'indonesia');
        
        // Memastikan panggilan API tetap sukses meskipun casing tidak standar
        $this->assertTrue($result['success'], "API harus sukses memproses input huruf kecil.");
        
        // Memastikan data utama tetap ada
        $this->assertArrayHasKey('timings', $result);
    }

    //--- Test Case Validasi Data Waktu Krusial (TC 9)---
    public function testValidatesCrucialPrayerTimes() {
        $client = new PrayerTimeClient('not_required'); 
        $result = $client->getDailyTimesByCity('Denpasar', 'Indonesia');
        
        // Memastikan Maghrib dan Isya ada dan formatnya benar
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('timings', $result);

        // Validasi Maghrib
        $this->assertArrayHasKey('Maghrib', $result['timings'], "Jadwal Maghrib harus ada.");
        $this->assertMatchesRegularExpression('/\d{2}:\d{2}/', $result['timings']['Maghrib'], "Waktu Maghrib harus format HH:MM");

        // Validasi Isha
        $this->assertArrayHasKey('Isha', $result['timings'], "Jadwal Isya harus ada.");
        $this->assertMatchesRegularExpression('/\d{2}:\d{2}/', $result['timings']['Isha'], "Waktu Isya harus format HH:MM");
    }
}