
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

$judul = $data["judul"];
$isi = $data["isi"];
$kategori = $data["kategori"];
$tanggal_terbit = $data["tanggal_terbit"];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = trim($_POST["judul"] ?? "");
    $isi = trim($_POST["isi"] ?? "");
    $kategori = $_POST["kategori"] ?? "";
    $tanggal_terbit = $_POST["tanggal_terbit"] ?? "";

    $kategori_valid = ["Akademik", "Ekstrakurikuler", "Umum"];

    $tanggal_valid =
        preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_terbit) &&
        checkdate(
            (int) substr($tanggal_terbit, 5, 2),
            (int) substr($tanggal_terbit, 8, 2),
            (int) substr($tanggal_terbit, 0, 4)
        );

    if ($judul === "" || $isi === "" || $tanggal_terbit === "") {
        $error = "Mohon lengkapi semua kolom.";
    } elseif (mb_strlen($judul) > 100) {
        $error = "Judul maksimal 100 karakter.";
    } elseif (!in_array($kategori, $kategori_valid, true)) {
        $error = "Pilih kategori yang tersedia.";
    } elseif (!$tanggal_valid) {
        $error = "Tanggal terbit tidak valid.";
    } else {
        $update = $pdo->prepare(
            "UPDATE pengumuman_sekolah
             SET judul = :judul,
                 isi = :isi,
                 kategori = :kategori,
                 tanggal_terbit = :tanggal
             WHERE id_pengumuman = :id"
        );

        $update->execute([
            ":judul" => $judul,
            ":isi" => $isi,
            ":kategori" => $kategori,
            ":tanggal" => $tanggal_terbit,
            ":id" => $id
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengumuman | EduBoard</title>

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

        .form-card,
        .preview-card {
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

        .form-label {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 9px;
        }

        .form-control,
        .form-select {
            border: 1px solid #dfe6f1;
            border-radius: 11px;
            padding: 12px 14px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(66, 99, 235, 0.12);
        }

        textarea.form-control {
            min-height: 180px;
            resize: vertical;
        }

        .btn-primary {
            background: var(--blue);
            border-color: var(--blue);
            border-radius: 11px;
            padding: 12px 18px;
            font-weight: 700;
        }

        .btn-primary:hover {
            background: #3451d1;
            border-color: #3451d1;
        }

        .btn-soft {
            background: #eef2f9;
            color: var(--navy);
            border: none;
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

        .preview-label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .preview-title {
            font-size: 23px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .preview-body {
            color: #526078;
            line-height: 1.8;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
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

        .info-box {
            background: #f5f7fc;
            border-radius: 13px;
            padding: 15px;
            color: #68758d;
            font-size: 12px;
            line-height: 1.7;
        }

        @media (max-width: 767px) {
            .form-card,
            .preview-card {
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
        <h1 class="fw-bold mb-2">Edit Pengumuman</h1>
        <p class="mb-0">
            Perbarui informasi pengumuman sekolah dengan mudah.
        </p>
    </div>
</header>

<main class="container main-content">
    <div class="row g-4">

        <div class="col-lg-7">
            <div class="form-card">
                <h2 class="section-title mb-1">Informasi Pengumuman</h2>
                <p class="text-secondary small mb-4">
                    Data lama sudah ditampilkan. Ubah bagian yang diperlukan.
                </p>

                <?php if ($error !== ""): ?>
                    <div class="alert alert-danger rounded-3">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label for="judul" class="form-label">
                            Judul Pengumuman
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="judul"
                            name="judul"
                            maxlength="100"
                            value="<?= e($judul) ?>"
                            placeholder="Masukkan judul pengumuman"
                            required
                        >

                        <div class="text-end small text-secondary mt-2">
                            <span id="charCount"><?= mb_strlen($judul) ?></span>/100 karakter
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="isi" class="form-label">
                            Isi Pengumuman
                        </label>

                        <textarea
                            class="form-control"
                            id="isi"
                            name="isi"
                            placeholder="Tuliskan isi pengumuman..."
                            required
                        ><?= e($isi) ?></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="kategori" class="form-label">
                                Kategori
                            </label>

                            <select
                                class="form-select"
                                id="kategori"
                                name="kategori"
                                required
                            >
                                <option value="">Pilih kategori</option>

                                <option value="Akademik"
                                    <?= $kategori === "Akademik" ? "selected" : "" ?>>
                                    Akademik
                                </option>

                                <option value="Ekstrakurikuler"
                                    <?= $kategori === "Ekstrakurikuler" ? "selected" : "" ?>>
                                    Ekstrakurikuler
                                </option>

                                <option value="Umum"
                                    <?= $kategori === "Umum" ? "selected" : "" ?>>
                                    Umum
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal_terbit" class="form-label">
                                Tanggal Terbit
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="tanggal_terbit"
                                name="tanggal_terbit"
                                value="<?= e($tanggal_terbit) ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            ✓ Simpan Perubahan
                        </button>

                        <a href="index.php" class="btn-soft">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="preview-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4 gap-2">
                    <h2 class="section-title mb-0">Pratinjau</h2>

                    <span class="category-badge" id="previewCategory">
                        <?= e($kategori) ?>
                    </span>
                </div>

                <div class="preview-label mb-2">Judul Pengumuman</div>
                <div class="preview-title mb-4" id="previewTitle">
                    <?= e($judul) ?>
                </div>

                <div class="preview-label mb-2">Isi Pengumuman</div>
                <div class="preview-body mb-4" id="previewIsi"><?= e($isi) ?></div>

                <hr>

                <div class="preview-label mb-2">Tanggal Terbit</div>
                <div class="fw-semibold" id="previewTanggal">
                    <?= e($tanggal_terbit) ?>
                </div>
            </div>

            <div class="info-box">
                <strong class="d-block mb-1">Tips Pengeditan</strong>
                Pastikan semua informasi sudah benar sebelum menyimpan.
                Perubahan akan diperbarui di database dan ditampilkan di halaman utama.
            </div>
        </div>

    </div>
</main>

<script>
    const judulInput = document.getElementById("judul");
    const isiInput = document.getElementById("isi");
    const kategoriInput = document.getElementById("kategori");
    const tanggalInput = document.getElementById("tanggal_terbit");

    judulInput.addEventListener("input", function () {
        document.getElementById("previewTitle").textContent =
            this.value || "Judul pengumuman";

        document.getElementById("charCount").textContent =
            this.value.length;
    });

    isiInput.addEventListener("input", function () {
        document.getElementById("previewIsi").textContent =
            this.value || "Isi pengumuman akan muncul di sini.";
    });

    kategoriInput.addEventListener("change", function () {
        document.getElementById("previewCategory").textContent =
            this.value || "Kategori";
    });

    tanggalInput.addEventListener("input", function () {
        document.getElementById("previewTanggal").textContent =
            this.value || "Tanggal terbit";
    });
</script>

</body>
</html>
