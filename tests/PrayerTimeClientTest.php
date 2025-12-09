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
}