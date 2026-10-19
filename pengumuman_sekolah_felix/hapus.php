
<?php
require_once "koneksi.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Ambil data pengumuman yang akan dihapus
$query = $pdo->prepare(
    "SELECT * FROM pengumuman_sekolah
     WHERE id_pengumuman = :id"
);
$query->execute([":id" => $id]);
$data = $query->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header("Location: index.php");
    exit;
}

$error = "";

// Hapus hanya setelah pengguna menekan tombol konfirmasi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $hapus = $pdo->prepare(
        "DELETE FROM pengumuman_sekolah
         WHERE id_pengumuman = :id"
    );

    $hapus->execute([":id" => $id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hapus Pengumuman | EduBoard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>
        :root {
            --navy: #101a35;
            --blue: #4263eb;
            --muted: #718096;
            --bg: #f4f7fc;
            --red: #dc3545;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--navy);
        }

        .navbar {
            background: var(--navy);
            padding: 18px 0;
        }

        .brand {
            color: white;
            font-size: 21px;
            font-weight: 800;
            text-decoration: none;
        }

        .brand span {
            color: #7da0ff;
        }

        .page-header {
            background: linear-gradient(120deg, #101a35, #294a9b);
            color: white;
            padding: 42px 0 76px;
        }

        .page-header p {
            color: #d7e1ff;
        }

        .main-content {
            margin-top: -42px;
            padding-bottom: 50px;
        }

        .delete-card,
        .detail-card {
            background: white;
            border: 1px solid #e8edf6;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 12px 35px rgba(24, 42, 86, 0.06);
        }

        .section-title {
            font-size: 19px;
            font-weight: 800;
        }

        .warning-icon {
            width: 66px;
            height: 66px;
            border-radius: 20px;
            background: #fff0f1;
            color: var(--red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 22px;
        }

        .category-badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 30px;
            background: #eaf0ff;
            color: #3154c8;
            font-size: 12px;
            font-weight: 800;
        }

        .detail-title {
            font-size: 22px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .detail-body {
            color: #526078;
            line-height: 1.8;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .detail-label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .btn-danger-custom {
            background: var(--red);
            color: white;
            border: none;
            border-radius: 11px;
            padding: 12px 18px;
            font-weight: 700;
        }

        .btn-danger-custom:hover {
            background: #bb2d3b;
            color: white;
        }

        .btn-soft {
            background: #eef2f9;
            color: var(--navy);
            border-radius: 11px;
            padding: 12px 18px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
        }

        .btn-soft:hover {
            background: #e1e8f5;
            color: var(--navy);
        }

        .warning-box {
            background: #fff5f5;
            border: 1px solid #ffe0e3;
            border-radius: 13px;
            padding: 15px;
            color: #8e3540;
            font-size: 12px;
            line-height: 1.7;
        }

        @media (max-width: 767px) {
            .delete-card,
            .detail-card {
                padding: 21px;
            }

            .page-header {
                padding: 32px 0 65px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php" class="brand">
            Edu<span>Board.</span>
        </a>

        <a href="index.php" class="btn btn-sm btn-outline-light rounded-3">
            ← Kembali
        </a>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <div class="small mb-2">MANAJEMEN PENGUMUMAN</div>
        <h1 class="fw-bold mb-2">Hapus Pengumuman</h1>
        <p class="mb-0">
            Periksa kembali informasi sebelum menghapus pengumuman.
        </p>
    </div>
</header>

<main class="container main-content">
    <div class="row g-4">

        <div class="col-lg-5">
            <div class="delete-card h-100">
                <div class="warning-icon">!</div>

                <h2 class="section-title mb-3">
                    Yakin ingin menghapus?
                </h2>

                <p class="text-secondary small mb-4">
                    Pengumuman ini akan dihapus secara permanen dari database.
                    Pastikan kamu memilih data yang benar.
                </p>

                <div class="warning-box mb-4">
                    <strong>Perhatian!</strong><br>
                    Setelah dihapus, pengumuman ini tidak akan muncul lagi
                    di halaman utama.
                </div>

                <form method="POST">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn-danger-custom">
                            Hapus Pengumuman
                        </button>

                        <a href="index.php" class="btn-soft">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="detail-card">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
                    <h2 class="section-title mb-0">
                        Detail Pengumuman
                    </h2>

                    <span class="category-badge">
                        <?= e($data["kategori"]) ?>
                    </span>
                </div>

                <div class="detail-label mb-2">
                    Judul Pengumuman
                </div>

                <div class="detail-title mb-4">
                    <?= e($data["judul"]) ?>
                </div>

                <div class="detail-label mb-2">
                    Isi Pengumuman
                </div>

                <div class="detail-body mb-4"><?= e($data["isi"]) ?></div>

                <hr>

                <div class="detail-label mb-2">
                    Tanggal Terbit
                </div>

                <div class="fw-semibold">
                    <?= e($data["tanggal_terbit"]) ?>
                </div>

                <div class="text-secondary small mt-3">
                    ID Pengumuman: #<?= e($data["id_pengumuman"]) ?>
                </div>
            </div>
        </div>

    </div>
</main>

</body>
</html>
