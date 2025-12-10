<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencari Jadwal Sholat</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
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
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at 20% 20%, rgba(10, 35, 66, 0.06), transparent 25%),
                        radial-gradient(circle at 80% 10%, rgba(0, 172, 155, 0.08), transparent 25%),
                        #f8f9fa;
            position: relative;
            overflow-x: hidden;
        }

        /* simple repeating pattern */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: radial-gradient(circle at 1px 1px, rgba(13, 110, 253, 0.08) 1px, transparent 0),
                              radial-gradient(circle at 1px 1px, rgba(255, 193, 7, 0.1) 1px, transparent 0);
            background-size: 80px 80px, 120px 120px;
            opacity: 0.7;
            z-index: -2;
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
            display: flex;
            align-items: center;
            padding: 60px 0 40px;
        }

        .hero {
            background: linear-gradient(120deg, var(--primary), #0c4eb0);
            color: white;
            border-radius: 24px;
            padding: 50px;
            box-shadow: 0 18px 40px rgba(13, 110, 253, 0.35);
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 193, 7, 0.2), transparent 70%);
            top: -160px;
            right: -120px;
        }

        .hero::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(10, 191, 159, 0.25), transparent 70%);
            bottom: -40px;
            left: -60px;
        }

        .badge-pill {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            border-radius: 999px;
            padding: 8px 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .mosque-emoji {
            font-size: 44px;
            filter: drop-shadow(0 6px 10px rgba(0,0,0,0.18));
        }

        .title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 540px;
        }

        .hero-cta .btn {
            padding: 12px 22px;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 12px 30px rgba(0,0,0,0.18);
        }

        .hero-cta .btn-secondary {
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
        }

        .stat-card {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            border-radius: 16px;
            padding: 16px;
            backdrop-filter: blur(6px);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .info-panel {
            background: linear-gradient(135deg, #ffffff, #f4f8ff);
            border-radius: 18px;
            padding: 32px 26px 28px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.12);
            position: relative;
            overflow: hidden;
            border: 1px solid #e4e9f2;
        }

        .info-panel::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(255, 193, 7, 0.2), transparent 70%);
            top: -80px;
            right: -60px;
            pointer-events: none;
        }

        .circle-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #0d6efd;
            color: #fff;
            font-size: 1.8rem;
            box-shadow: 0 10px 24px rgba(13,110,253,0.25);
        }

        .mini-card {
            background: #fff;
            border-radius: 14px;
            padding: 14px;
            border: 1px solid #e7ecf3;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }

        /* pastikan judul doa/shalawat terbaca jelas */
        .info-panel .mini-card .fw-bold {
            color: #0a2342;
        }

        .info-panel .mini-card small {
            color: #6c757d;
        }

        .info-heading {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.2px;
            color: #0a2342;
            text-transform: uppercase;
        }

        .info-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .info-header h5 {
            color: #000000
        }

        .list-check i {
            font-size: 1rem;
        }

        .features {
            margin-top: 40px;
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

        .section-title {
            font-weight: 800;
            letter-spacing: -0.4px;
        }

        .divider {
            height: 10px;
            width: 80px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
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
        <div class="hero">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="badge-pill mb-3">
                        <i class="bi bi-stars"></i>
                        Ramah muslim • Cepat • Akurat
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="mosque-emoji">🕌</span>
                        <div>
                            <h1 class="title mb-1">Pencari Jadwal Sholat</h1>
                            <p class="subtitle mb-0">Temukan waktu sholat harian berdasarkan lokasi Anda, lengkap dengan informasi arah kiblat dan pengingat.</p>
                        </div>
                    </div>
                    <div class="hero-cta d-flex flex-wrap gap-2 mt-4">
                        <a class="btn btn-light text-primary" href="cari_lokasi.php">
                            <i class="bi bi-geo-alt-fill me-2"></i>Cari lokasi sekarang
                        </a>
                        <a class="btn btn-secondary" href="fitur.php">
                            <i class="bi bi-moon-stars me-2"></i>Lihat fitur
                        </a>
                    </div>
                    <div class="row g-3 mt-4">
                        <div class="col-6 col-md-4">
                            <div class="stat-card">
                                <div class="stat-value">34k+</div>
                                <div class="small">Lokasi didukung</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-card">
                                <div class="stat-value">99%</div>
                                <div class="small">Akurasi jadwal</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-card">
                                <div class="stat-value">24/7</div>
                                <div class="small">Pembaru­an data</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="info-panel">
                        <div class="info-header mb-2">
                            <div class="circle-icon">
                                <i class="bi bi-moon-stars-fill"></i>
                            </div>
                            <div>
                                <div class="info-heading">Ruang Islami</div>
                                <h5 class="mb-0">Hadirkan ketenangan</h5>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Temani ibadahmu dengan berbagai pilihan doa dan sholawat utama sepanjang hari.</p>

                        <div class="mini-card mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">Do'a Nabi Musa AS.</div>
                                    <small class="text-muted">“Rabbishrah li sadri, wa yassir li amri, wahlul 'uqdatan min lisani, yafqahu qawli”</small>
                                </div>
                                <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold">setiap hari</span>
                            </div>
                        </div>

                        <div class="mini-card mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">Do'a Nabi Yunus AS.</div>
                                    <small class="text-muted">“Lailaha illa Anta subhanaka inni kuntu minadhdhalimin”</small>
                                </div>
                                <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">setiap malam</span>
                            </div>
                        </div>

                        <div class="mini-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">Sholawat Bani Hasyim</div>
                                    <small class="text-muted">“Allâhumma shalli 'alân-nabiyil Hasyimiyyi Muhammadin wa 'alâ âlihi wa sallim taslîmân”</small>
                                </div>
                                <span class="badge rounded-pill bg-warning-subtle text-warning fw-semibold">setiap hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
