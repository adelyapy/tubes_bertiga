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
            <div class="col-md-6 col-lg-5">
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
</script>
</body>
</html>

