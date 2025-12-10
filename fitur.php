<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitur - Pencari Jadwal Sholat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #0abf9f;
            --accent: #ffc107;
            --dark: #0a2342;
        }
        body {
            min-height: 100vh;
            background: radial-gradient(circle at 20% 20%, rgba(10, 35, 66, 0.06), transparent 25%),
                        radial-gradient(circle at 80% 10%, rgba(0, 172, 155, 0.08), transparent 25%),
                        #f8f9fa;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: linear-gradient(135deg, var(--primary), #1658d5);
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .navbar-brand,
        .nav-link {
            color: white !important;
        }
        .nav-link:hover {
            color: var(--accent) !important;
        }
        .main-content {
            flex: 1;
            padding: 60px 0;
        }
        .feature-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 34px rgba(0,0,0,0.12);
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
        }
        .reminder-btn {
            border-radius: 12px;
            font-weight: 700;
        }
        .reminder-status {
            font-size: 0.9rem;
            color: #198754;
        }
        .page-title {
            font-weight: 800;
            letter-spacing: -0.4px;
        }
        .info-heading {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.2px;
            color: #0a2342;
            text-transform: uppercase;
        }

        /* Kiblat */
        .qibla-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            height: 100%;
        }
        .compass {
            width: 180px;
            height: 180px;
            border: 10px solid #e9ecef;
            border-radius: 50%;
            position: relative;
            margin: 0 auto 16px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.06);
            background: radial-gradient(circle, #fff 0%, #f8fafc 70%, #eef2f7 100%);
        }
        .compass::after {
            content: "N";
            position: absolute;
            top: 6px;
            left: 50%;
            transform: translateX(-50%);
            font-weight: 700;
            color: #6c757d;
        }
        .arrow {
            position: absolute;
            width: 0;
            height: 0;
            border-left: 12px solid transparent;
            border-right: 12px solid transparent;
            border-bottom: 60px solid #0d6efd;
            top: 30px;
            left: 50%;
            transform: translateX(-50%) rotate(0deg);
            transform-origin: 50% 60px;
            transition: transform 0.3s ease;
            filter: drop-shadow(0 6px 10px rgba(13,110,253,0.25));
        }
        .qibla-info small {
            color: #6c757d;
        }
        
        /* Kalender Islami */
        .hijri-calendar-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            height: 100%;
        }
        .hijri-date-display {
            background: linear-gradient(135deg,rgb(80, 148, 231) 0%,rgb(203, 100, 52) 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        .hijri-date-display .hijri-day {
            font-size: 2.5rem;
            font-weight: bold;
            line-height: 1;
        }
        .hijri-date-display .hijri-month-year {
            font-size: 1.1rem;
            opacity: 0.95;
            margin-top: 8px;
        }
        .hijri-date-display .gregorian-date {
            font-size: 0.9rem;
            opacity: 0.85;
            margin-top: 5px;
        }
        .hijri-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-top: 15px;
        }
        .hijri-day-name {
            text-align: center;
            font-weight: 600;
            font-size: 0.85rem;
            color: #6c757d;
            padding: 5px;
        }
        .hijri-day-cell {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        .hijri-day-cell:hover {
            background: #e9ecef;
            transform: scale(1.05);
        }
        .hijri-day-cell.today {
            background: linear-gradient(135deg, #8B4513, #A0522D);
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(139, 69, 19, 0.3);
        }
        .hijri-day-cell.other-month {
            opacity: 0.4;
        }
        .hijri-loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
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
            </ul>
        </div>
    </div>
</nav>

<main class="main-content">
    <div class="container">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="divider" style="height: 10px; width: 80px; border-radius: 999px; background: linear-gradient(90deg, var(--secondary), var(--primary));"></div>
            <h1 class="page-title h3 mb-0">Fitur Unggulan</h1>
        </div>
        <div class="row g-3 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card feature-card h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-info mb-3">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h5 class="card-title">Pengingat sholat otomatis</h5>
                        <p class="card-text text-muted">Dapatkan notifikasi 5 menit sebelum waktu sholat. Aktifkan agar tab ini bisa memberi popup/suara.</p>
                        <button class="btn btn-primary reminder-btn mb-2" id="enable-reminder">
                            <i class="bi bi-bell me-1"></i>Aktifkan pengingat
                        </button>
                        <div id="reminder-status" class="reminder-status small"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card qibla-card h-100">
                    <div class="card-body text-center">
                        <div class="feature-icon mb-3" style="background:#0abf9f;">
                            <i class="bi bi-compass"></i>
                        </div>
                        <h5 class="card-title">Pencari arah kiblat</h5>
                        <p class="card-text text-muted">
                            Gunakan lokasi perangkat untuk mendapatkan arah kiblat (Ka'bah) dari posisi Anda.
                        </p>
                        <div class="compass my-3">
                            <div class="arrow" id="qibla-arrow"></div>
                        </div>
                        <div class="qibla-info mb-3">
                            <div class="fw-bold" id="qibla-bearing">-°</div>
                            <small id="qibla-status">Klik "Cari arah" untuk mulai.</small>
                        </div>
                        <button class="btn btn-success w-100" id="btn-qibla">
                            <i class="bi bi-geo-alt me-1"></i>Cari arah
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Card Kalender Islami -->
            <div class="col-md-6 col-lg-4">
                <div class="card hijri-calendar-card h-100">
                    <div class="card-body">
                        <div class="feature-icon mb-3" style="background: linear-gradient(135deg,rgb(108, 78, 230),rgb(82, 107, 235));">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <h5 class="card-title">Kalender Islami (Hijriah)</h5>
                        <p class="card-text text-muted">
                            Lihat tanggal Hijriah saat ini dan kalender bulanan Islami.
                        </p>
                        
                        <div id="hijri-calendar-container">
                            <div class="hijri-loading">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Memuat kalender...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        const prayerTimes = {
            Fajr: "05:00",
            Dhuhr: "12:00",
            Asr: "15:30",
            Maghrib: "17:50",
            Isha: "19:00"
        };
        const minutesBefore = 5;
        let timeouts = [];

        function clearReminders() {
            timeouts.forEach(t => clearTimeout(t));
            timeouts = [];
        }

        function parseToday(timeStr) {
            const [hh, mm] = timeStr.split(":").map(Number);
            const d = new Date();
            d.setHours(hh, mm, 0, 0);
            return d;
        }

        function scheduleReminders() {
            clearReminders();
            const now = new Date();
            Object.entries(prayerTimes).forEach(([name, timeStr]) => {
                const t = parseToday(timeStr);
                t.setMinutes(t.getMinutes() - minutesBefore);
                const diff = t.getTime() - now.getTime();
                if (diff > 0) {
                    const timer = setTimeout(() => notify(name), diff);
                    timeouts.push(timer);
                }
            });
        }

        function notify(name) {
            const title = `Pengingat ${name}`;
            const body = `${minutesBefore} menit menuju waktu ${name}.`;
            if ("Notification" in window && Notification.permission === "granted") {
                const n = new Notification(title, { body });
                setTimeout(() => n.close(), 8000);
            } else {
                alert(body);
            }
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = "sine";
                osc.frequency.value = 880;
                gain.gain.value = 0.05;
                osc.connect(gain).connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.5);
            } catch (e) {}
        }

        async function enableReminder() {
            if (!("Notification" in window)) {
                setStatus("Browser tidak mendukung notifikasi.", true);
                return;
            }
            if (Notification.permission !== "granted") {
                const perm = await Notification.requestPermission();
                if (perm !== "granted") {
                    setStatus("Izin notifikasi ditolak.", true);
                    return;
                }
            }
            scheduleReminders();
            setStatus("Pengingat aktif. Biarkan tab ini terbuka.", false);
        }

        function setStatus(msg, isError) {
            const el = document.getElementById("reminder-status");
            if (!el) return;
            el.textContent = msg;
            el.style.color = isError ? "#dc3545" : "#198754";
        }

        document.getElementById("enable-reminder")?.addEventListener("click", enableReminder);
    })();

    // --- Pencari arah kiblat ---
    (function() {
        const btn = document.getElementById('btn-qibla');
        const arrow = document.getElementById('qibla-arrow');
        const statusEl = document.getElementById('qibla-status');
        const bearingEl = document.getElementById('qibla-bearing');
        const KAABA = { lat: 21.422487, lon: 39.826206 };

        function toRad(deg) { return deg * Math.PI / 180; }
        function toDeg(rad) { return rad * 180 / Math.PI; }

        function computeBearing(lat1, lon1, lat2, lon2) {
            const dLon = toRad(lon2 - lon1);
            const y = Math.sin(dLon) * Math.cos(toRad(lat2));
            const x = Math.cos(toRad(lat1)) * Math.sin(toRad(lat2)) -
                      Math.sin(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.cos(dLon);
            const brng = Math.atan2(y, x);
            return (toDeg(brng) + 360) % 360;
        }

        function setStatus(msg, isError = false) {
            if (!statusEl) return;
            statusEl.textContent = msg;
            statusEl.style.color = isError ? '#dc3545' : '#6c757d';
        }

        function updateArrow(deg) {
            if (!arrow) return;
            arrow.style.transform = `translateX(-50%) rotate(${deg}deg)`;
        }

        function handleSuccess(pos) {
            const { latitude, longitude } = pos.coords;
            const bearing = computeBearing(latitude, longitude, KAABA.lat, KAABA.lon);
            bearingEl.textContent = `${bearing.toFixed(1)}°`;
            setStatus('Arah relatif terhadap utara sejati. Hadapkan perangkat ke utara.');
            updateArrow(bearing);
        }

        function handleError(err) {
            console.error(err);
            setStatus('Gagal mengambil lokasi. Izinkan akses lokasi dan coba lagi.', true);
        }

        btn?.addEventListener('click', () => {
            if (!navigator.geolocation) {
                setStatus('Geolocation tidak didukung browser ini.', true);
                return;
            }
            setStatus('Mengambil lokasi...');
            navigator.geolocation.getCurrentPosition(handleSuccess, handleError, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        });
    })();

    // --- Kalender Islami (Hijriah) ---
    (function() {
        const container = document.getElementById('hijri-calendar-container');
        if (!container) return;

        const hijriMonths = [
            'Muharram', 'Safar', 'Rabi\' al-awwal', 'Rabi\' al-thani',
            'Jumada al-awwal', 'Jumada al-thani', 'Rajab', 'Sha\'ban',
            'Ramadan', 'Shawwal', 'Dhu al-Qi\'dah', 'Dhu al-Hijjah'
        ];

        const dayNames = ['Ah', 'Se', 'Se', 'Ra', 'Kh', 'Ju', 'Sa'];

        async function loadHijriCalendar() {
            try {
                const today = new Date();
                const year = today.getFullYear();
                const month = today.getMonth() + 1;
                const day = today.getDate();
                
                // Menggunakan endpoint yang lebih tepat dengan parameter lengkap
                // Endpoint calendar memerlukan lokasi, jadi kita gunakan endpoint gToH untuk konversi
                // atau gunakan calendarByCity dengan lokasi default
                
                // Coba menggunakan endpoint gToH untuk konversi tanggal hari ini
                const gToHResponse = await fetch(`https://api.aladhan.com/v1/gToH/${day}-${month}-${year}`);
                const gToHData = await gToHResponse.json();
                
                if (gToHData.code === 200 && gToHData.data) {
                    // Ambil data kalender bulan ini menggunakan calendarByCity
                    // Gunakan Jakarta sebagai default karena tidak memerlukan lokasi spesifik
                    const calendarResponse = await fetch(`https://api.aladhan.com/v1/calendarByCity?city=Jakarta&country=Indonesia&month=${month}&year=${year}&method=8`);
                    const calendarData = await calendarResponse.json();
                    
                    if (calendarData.code === 200 && calendarData.data) {
                        renderHijriCalendar(calendarData.data, today, gToHData.data);
                    } else {
                        // Fallback: hanya tampilkan tanggal hari ini
                        renderTodayOnly(gToHData.data, today);
                    }
                } else {
                    throw new Error('Gagal memuat data konversi tanggal');
                }
            } catch (error) {
                console.error('Error loading hijri calendar:', error);
                // Fallback: coba metode alternatif
                try {
                    await loadHijriCalendarFallback();
                } catch (fallbackError) {
                    console.error('Fallback also failed:', fallbackError);
                    container.innerHTML = `
                        <div class="hijri-loading text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Gagal memuat kalender. Pastikan koneksi internet aktif.
                            <br><small class="mt-2 d-block">Error: ${error.message}</small>
                        </div>
                    `;
                }
            }
        }

        async function loadHijriCalendarFallback() {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth() + 1;
            const day = today.getDate();
            
            // Gunakan endpoint yang lebih sederhana
            const response = await fetch(`https://api.aladhan.com/v1/calendarByCity?city=Jakarta&country=Indonesia&month=${month}&year=${year}&method=8`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.code === 200 && data.data && Array.isArray(data.data)) {
                renderHijriCalendar(data.data, today, null);
            } else {
                throw new Error('Format data tidak valid');
            }
        }

        function renderTodayOnly(hijriData, today) {
            if (!hijriData || !hijriData.hijri) return;
            
            const hijri = hijriData.hijri;
            const hijriDay = hijri.day;
            const hijriMonth = hijriMonths[parseInt(hijri.month.number) - 1];
            const hijriYear = hijri.year;
            const gregorianDate = today.toLocaleDateString('id-ID', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });

            container.innerHTML = `
                <div class="hijri-date-display">
                    <div class="hijri-day">${hijriDay}</div>
                    <div class="hijri-month-year">${hijriMonth} ${hijriYear} H</div>
                    <div class="gregorian-date">${gregorianDate}</div>
                </div>
                <div class="text-center text-muted small mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Kalender bulanan sedang dimuat...
                </div>
            `;
        }

        function renderHijriCalendar(calendarData, today, todayHijriData) {
            // Cari data hari ini
            const todayStr = today.toISOString().split('T')[0];
            let todayHijri = null;

            // Jika todayHijriData diberikan, gunakan itu
            if (todayHijriData && todayHijriData.hijri) {
                todayHijri = todayHijriData.hijri;
            } else {
                // Cari dari calendarData
                calendarData.forEach(day => {
                    if (day.date && day.date.gregorian && day.date.gregorian.date === todayStr) {
                        todayHijri = day.date.hijri;
                    }
                });
            }

            // Jika masih tidak ada, ambil dari index pertama
            if (!todayHijri && calendarData.length > 0 && calendarData[0].date) {
                todayHijri = calendarData[0].date.hijri;
            }

            // Render tampilan
            if (todayHijri) {
                const hijriDay = todayHijri.day;
                const hijriMonth = hijriMonths[parseInt(todayHijri.month.number) - 1];
                const hijriYear = todayHijri.year;
                const gregorianDate = today.toLocaleDateString('id-ID', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });

                let html = `
                    <div class="hijri-date-display">
                        <div class="hijri-day">${hijriDay}</div>
                        <div class="hijri-month-year">${hijriMonth} ${hijriYear} H</div>
                        <div class="gregorian-date">${gregorianDate}</div>
                    </div>
                `;

                // Render kalender grid sederhana
                html += '<div class="hijri-calendar-grid">';
                
                // Header hari
                dayNames.forEach(day => {
                    html += `<div class="hijri-day-name">${day}</div>`;
                });

                // Ambil 14 hari dari calendarData
                let dayCount = 0;
                const todayIndex = calendarData.findIndex(d => 
                    d.date && d.date.gregorian && d.date.gregorian.date === todayStr
                );
                
                const startIndex = Math.max(0, (todayIndex >= 0 ? todayIndex : 0) - 3);
                const endIndex = Math.min(calendarData.length, startIndex + 14);

                for (let i = startIndex; i < endIndex && dayCount < 14; i++) {
                    if (!calendarData[i] || !calendarData[i].date) continue;
                    
                    const day = calendarData[i];
                    const isToday = day.date.gregorian && day.date.gregorian.date === todayStr;
                    const hijriDayNum = parseInt(day.date.hijri.day);
                    
                    html += `
                        <div class="hijri-day-cell ${isToday ? 'today' : ''}" 
                             title="${day.date.hijri.day} ${hijriMonths[parseInt(day.date.hijri.month.number) - 1]} ${day.date.hijri.year} H">
                            ${hijriDayNum}
                        </div>
                    `;
                    dayCount++;
                }

                // Isi sisa grid jika kurang dari 14 hari
                while (dayCount < 14) {
                    html += '<div class="hijri-day-cell other-month"></div>';
                    dayCount++;
                }

                html += '</div>';
                container.innerHTML = html;
            } else {
                throw new Error('Data Hijriah tidak ditemukan');
            }
        }

        // Load kalender saat halaman dimuat
        loadHijriCalendar();
    })();
</script>
</body>
</html>

