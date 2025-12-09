<?php
// cari_lokasi.php

// Redirect ke halaman hasil jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = urlencode($_POST['city'] ?? '');
    $country = urlencode($_POST['country'] ?? '');
    $date = urlencode($_POST['date'] ?? date('Y-m-d')); // Tambahkan parameter tanggal
    header("Location: hasil.php?city=" . $city . "&country=" . $country . "&date=" . $date);
    exit;
}

$city = '';
$country = '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Lokasi - Pencari Jadwal Sholat</title>
    
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
        
        .nav-link.active {
            color: #ffc107 !important;
            font-weight: bold;
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
                    <a class="nav-link active" href="cari_lokasi.php">
                        <i class="bi bi-search me-1"></i>Cari Lokasi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4 shadow">
                <h5 class="card-title text-center mb-4">Cari Lokasi</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label for="city" class="form-label">Kota</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($city) ?>" placeholder="Masukkan nama kota" required>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Negara</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?= htmlspecialchars($country) ?>" placeholder="Masukkan nama negara" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Cari Jadwal
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

