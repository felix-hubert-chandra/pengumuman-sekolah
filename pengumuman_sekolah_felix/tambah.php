
<?php
require_once "koneksi.php";

$judul = "";
$isi = "";
$kategori = "";
$tanggal_terbit = date("Y-m-d");
$error = "";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = trim($_POST["judul"] ?? "");
    $isi = trim($_POST["isi"] ?? "");
    $kategori = $_POST["kategori"] ?? "";
    $tanggal_terbit = $_POST["tanggal_terbit"] ?? "";

    $kategori_valid = ["Akademik", "Ekstrakurikuler", "Umum"];

    $tanggal_valid =
        preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_terbit) &&
        checkdate(
            (int)substr($tanggal_terbit, 5, 2),
            (int)substr($tanggal_terbit, 8, 2),
            (int)substr($tanggal_terbit, 0, 4)
        );

    if ($judul === "" || $isi === "" || $tanggal_terbit === "") {
        $error = "Mohon lengkapi semua kolom terlebih dahulu.";
    } elseif (mb_strlen($judul) > 100) {
        $error = "Judul maksimal 100 karakter.";
    } elseif (!in_array($kategori, $kategori_valid, true)) {
        $error = "Silakan pilih kategori yang tersedia.";
    } elseif (!$tanggal_valid) {
        $error = "Tanggal terbit tidak valid.";
    } else {
        $query = $pdo->prepare(
            "INSERT INTO pengumuman_sekolah
            (judul, isi, kategori, tanggal_terbit)
            VALUES (:judul, :isi, :kategori, :tanggal)"
        );

        $query->execute([
            ":judul" => $judul,
            ":isi" => $isi,
            ":kategori" => $kategori,
            ":tanggal" => $tanggal_terbit
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Pengumuman | EduBoard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>
        :root {
            --navy: #101a35;
            --blue: #526dff;
            --muted: #8994ad;
            --line: #e9edf6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f5f7fc;
            color: var(--navy);
        }

        .navbar {
            background: var(--navy);
            padding: 17px 0;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .brand {
            font-size: 21px;
            font-weight: 800;
            color: white;
            text-decoration: none;
            letter-spacing: -.7px;
        }

        .brand-mark {
            display: inline-flex;
            width: 37px;
            height: 37px;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #7385ff, #4355ec);
            border-radius: 12px;
            margin-right: 9px;
            box-shadow: 0 5px 18px rgba(82,109,255,.35);
        }

        .nav-back {
            color: #e3e9ff;
            border: 1px solid #3b4664;
            border-radius: 10px;
            text-decoration: none;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 600;
        }

        .nav-back:hover {
            color: white;
            background: #253353;
        }

        .page-wrap {
            max-width: 1150px;
            margin: auto;
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 34px;
            color: white;
            border-radius: 24px;
            background:
                radial-gradient(circle at 90% 10%,
                    rgba(121,142,255,.45), transparent 30%),
                linear-gradient(120deg, #131e3d, #3049a4);
            box-shadow: 0 15px 40px rgba(28,49,109,.13);
        }

        .hero::after {
            content: "";
            position: absolute;
            right: -55px;
            bottom: -140px;
            width: 270px;
            height: 270px;
            border: 42px solid rgba(255,255,255,.07);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid rgba(255,255,255,.17);
            background: rgba(255,255,255,.08);
            color: #dce3ff;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .hero h1 {
            font-size: clamp(27px, 4vw, 38px);
            font-weight: 800;
            letter-spacing: -1.5px;
            margin-top: 18px;
        }

        .hero p {
            color: #c3cdeb;
            max-width: 510px;
            font-size: 13px;
            line-height: 1.9;
        }

        .hero-icon {
            position: absolute;
            right: 45px;
            top: 35px;
            width: 105px;
            height: 105px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 45px;
            color: #e0e6ff;
            background: rgba(255,255,255,.09);
            border: 1px solid rgba(255,255,255,.13);
            border-radius: 30px;
            transform: rotate(7deg);
        }

        .step-track {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 24px 0;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #929db3;
            font-size: 11px;
            font-weight: 600;
        }

        .step-number {
            width: 29px;
            height: 29px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8ecf5;
            color: #68758d;
        }

        .step.active {
            color: #425de5;
        }

        .step.active .step-number {
            background: #526dff;
            color: white;
            box-shadow: 0 4px 12px #526dff40;
        }

        .step-line {
            height: 1px;
            flex: 1;
            max-width: 60px;
            background: #e0e5f0;
        }

        .content-card {
            background: white;
            border: 1px solid #edf0f7;
            border-radius: 22px;
            box-shadow: 0 8px 35px rgba(29,48,91,.045);
        }

        .section-heading {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .section-subtitle {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.8;
        }

        .field-label {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #293754;
        }

        .required {
            color: #f05b75;
        }

        .form-control, .form-select {
            padding: 13px 14px;
            border: 1px solid #e4e9f3;
            border-radius: 12px;
            color: #263552;
            font-family: inherit;
            font-size: 12px;
            background-color: #fcfdff;
            transition: .2s;
        }

        .form-control::placeholder {
            color: #aab3c4;
        }

        .form-control:focus, .form-select:focus {
            background: white;
            border-color: #7185ff;
            box-shadow: 0 0 0 4px rgba(82,109,255,.10);
        }

        textarea.form-control {
            min-height: 160px;
            resize: vertical;
            line-height: 1.9;
        }

        .field-hint {
            color: #98a2b6;
            font-size: 10px;
            margin-top: 8px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon .form-control {
            padding-left: 42px;
        }

        .input-icon span {
            position: absolute;
            top: 12px;
            left: 15px;
            color: #8996b2;
            font-size: 14px;
        }

        .preview-card {
            position: sticky;
            top: 20px;
            overflow: hidden;
            background: white;
            border: 1px solid #e9edf6;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(29,48,91,.05);
        }

        .preview-top {
            background: linear-gradient(135deg, #172445, #4058b7);
            color: white;
            padding: 23px;
        }

        .preview-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.2px;
            color: #c7d2ff;
        }

        .preview-paper {
            padding: 23px;
            min-height: 195px;
        }

        .preview-title {
            font-size: 17px;
            line-height: 1.5;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .preview-body {
            font-size: 11px;
            color: #75819a;
            line-height: 1.9;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .preview-badge {
            display: inline-block;
            border-radius: 7px;
            padding: 7px 10px;
            font-size: 10px;
            font-weight: 700;
            background: #e8edff;
            color: #435ce4;
        }

        .preview-date {
            color: #8b96aa;
            font-size: 10px;
        }

        .preview-note {
            background: #f5f7fc;
            color: #7d8aa4;
            font-size: 10px;
            line-height: 1.8;
            padding: 14px;
            border-radius: 12px;
            margin-top: 18px;
        }

        .btn-submit {
            border: none;
            border-radius: 12px;
            padding: 13px 19px;
            background: linear-gradient(120deg, #526dff, #4055e5);
            color: white;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 7px 16px rgba(82,109,255,.22);
            transition: .2s;
        }

        .btn-submit:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(82,109,255,.28);
        }

        .btn-cancel {
            border: 1px solid #e3e8f2;
            border-radius: 12px;
            padding: 13px 19px;
            color: #52617c;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
        }

        .btn-cancel:hover {
            background: #f5f7fc;
            color: #273754;
        }

        .footer {
            color: #9aa4b7;
            text-align: center;
            font-size: 10px;
            padding: 25px 0;
        }

        @media (max-width: 767px) {
            .hero {
                padding: 25px;
            }

            .hero-icon {
                right: 18px;
                top: 20px;
                width: 55px;
                height: 55px;
                font-size: 25px;
                border-radius: 17px;
            }

            .hero h1 {
                max-width: 80%;
            }

            .step {
                font-size: 9px;
                gap: 5px;
            }

            .step-number {
                width: 25px;
                height: 25px;
            }

            .preview-card {
                position: static;
            }
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container page-wrap d-flex justify-content-between align-items-center">
        <a href="index.php" class="brand">
            <span class="brand-mark">✦</span>Edu<span style="color:#8fa2ff">Board</span>
        </a>

        <a href="index.php" class="nav-back">← Dashboard</a>
    </div>
</nav>

<main class="container page-wrap py-4 py-md-5">

    <section class="hero">
        <div class="hero-content">
            <span class="eyebrow">✦ &nbsp; PORTAL INFORMASI SEKOLAH</span>

            <h1>Bagikan Informasi<br>Menjadi Lebih Mudah.</h1>

            <p class="mb-0 mt-3">
                Buat pengumuman baru untuk menyampaikan informasi
                akademik dan kegiatan sekolah secara terstruktur.
            </p>
        </div>

        <div class="hero-icon" aria-hidden="true">✎</div>
    </section>

    <div class="step-track">
        <div class="step active">
            <span class="step-number">1</span>
            <span>Isi Informasi</span>
        </div>

        <div class="step-line"></div>

        <div class="step">
            <span class="step-number">2</span>
            <span>Periksa Preview</span>
        </div>

        <div class="step-line"></div>

        <div class="step">
            <span class="step-number">3</span>
            <span>Terbitkan</span>
        </div>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger border-0 rounded-4 mb-4">
            <strong>Periksa kembali.</strong>
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="tambah.php">
        <div class="row g-4">

            <div class="col-lg-7">
                <div class="content-card p-4 p-md-5">

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="brand-mark text-white flex-shrink-0"
                             style="margin:0;width:43px;height:43px;">
                            ✧
                        </div>

                        <div>
                            <h2 class="section-heading mb-1">
                                Detail Pengumuman
                            </h2>

                            <p class="section-subtitle mb-0">
                                Lengkapi informasi di bawah ini.
                                Semua kolom wajib diisi.
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="judul" class="field-label">
                            Judul Pengumuman <span class="required">*</span>
                        </label>

                        <div class="input-icon">
                            <span>✎</span>
                            <input type="text"
                                   id="judul"
                                   name="judul"
                                   class="form-control"
                                   maxlength="100"
                                   placeholder="Contoh: Jadwal Ujian Semester"
                                   value="<?= e($judul) ?>"
                                   required>
                        </div>

                        <div class="d-flex justify-content-between field-hint">
                            <span>Gunakan judul yang jelas dan singkat.</span>
                            <span id="judulCount">0/100</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="isi" class="field-label">
                            Isi Pengumuman <span class="required">*</span>
                        </label>

                        <textarea id="isi"
                                  name="isi"
                                  class="form-control"
                                  placeholder="Jelaskan informasi, jadwal, lokasi, atau hal penting lainnya..."
                                  required><?= e($isi) ?></textarea>

                        <div class="field-hint">
                            Tuliskan informasi dengan bahasa yang jelas.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="kategori" class="field-label">
                                Kategori <span class="required">*</span>
                            </label>

                            <select id="kategori"
                                    name="kategori"
                                    class="form-select"
                                    required>
                                <option value="">Pilih kategori</option>

                                <option value="Akademik"
                                    <?= $kategori === "Akademik" ? "selected" : "" ?>>
                                    📘 Akademik
                                </option>

                                <option value="Ekstrakurikuler"
                                    <?= $kategori === "Ekstrakurikuler" ? "selected" : "" ?>>
                                    🎯 Ekstrakurikuler
                                </option>

                                <option value="Umum"
                                    <?= $kategori === "Umum" ? "selected" : "" ?>>
                                    📢 Umum
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal_terbit" class="field-label">
                                Tanggal Terbit <span class="required">*</span>
                            </label>

                            <input type="date"
                                   id="tanggal_terbit"
                                   name="tanggal_terbit"
                                   class="form-control"
                                   value="<?= e($tanggal_terbit) ?>"
                                   required>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color:#edf0f7">

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <span class="field-hint">
                            * Pastikan data sudah benar.
                        </span>

                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn-cancel">
                                Batal
                            </a>

                            <button type="submit" class="btn-submit">
                                Terbitkan ↗
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-5">
                <aside class="preview-card">
                    <div class="preview-top">
                        <div class="preview-label">LIVE PREVIEW</div>

                        <h5 class="fw-bold mt-3 mb-1">
                            Tampilan Pengumuman
                        </h5>

                        <p class="mb-0 small"
                           style="color:#cbd5ff;font-size:11px;">
                            Pratinjau akan berubah saat kamu mengetik.
                        </p>
                    </div>

                    <div class="preview-paper">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="preview-badge" id="previewKategori">
                                Kategori
                            </span>

                            <span class="preview-date" id="previewTanggal">
                                Tanggal terbit
                            </span>
                        </div>

                        <h3 class="preview-title mb-3" id="previewJudul">
                            Judul pengumuman kamu
                        </h3>

                        <p class="preview-body mb-0" id="previewIsi">Isi pengumuman akan tampil di sini. Pastikan informasi yang ditulis jelas dan mudah dipahami.</p>

                        <hr style="border-color:#edf0f7;margin:23px 0 15px">

                        <div class="d-flex align-items-center gap-2">
                            <span class="brand-mark"
                                  style="width:30px;height:30px;border-radius:9px;font-size:12px;margin:0;">
                                ✦
                            </span>

                            <div>
                                <div style="font-size:10px;font-weight:700;">
                                    EduBoard
                                </div>

                                <div style="font-size:9px;color:#98a2b6;">
                                    Portal Informasi Sekolah
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="preview-note">
                    <strong>✦ Tips membuat pengumuman</strong>
                    <br>
                    Gunakan judul yang spesifik, sertakan tanggal atau
                    jadwal penting, dan pilih kategori yang sesuai.
                </div>
            </div>

        </div>
    </form>

    <footer class="footer">
        EduBoard · PHP Native · PDO · MySQL · Bootstrap
    </footer>

</main>

<script>
    const judul = document.getElementById("judul");
    const isi = document.getElementById("isi");
    const kategori = document.getElementById("kategori");
    const tanggal = document.getElementById("tanggal_terbit");

    function updatePreview() {
        document.getElementById("previewJudul").textContent =
            judul.value.trim() || "Judul pengumuman kamu";

        document.getElementById("previewIsi").textContent =
            isi.value.trim() ||
            "Isi pengumuman akan tampil di sini. Pastikan informasi yang ditulis jelas dan mudah dipahami.";

        document.getElementById("previewKategori").textContent =
            kategori.value || "Kategori";

        document.getElementById("previewTanggal").textContent =
            tanggal.value
                ? new Date(tanggal.value + "T00:00:00").toLocaleDateString(
                    "id-ID",
                    { day: "2-digit", month: "short", year: "numeric" }
                  )
                : "Tanggal terbit";

        document.getElementById("judulCount").textContent =
            judul.value.length + "/100";

        const badge = document.getElementById("previewKategori");

        if (kategori.value === "Ekstrakurikuler") {
            badge.style.background = "#dff8ed";
            badge.style.color = "#16815b";
        } else if (kategori.value === "Umum") {
            badge.style.background = "#fff0d9";
            badge.style.color = "#b77512";
        } else {
            badge.style.background = "#e8edff";
            badge.style.color = "#435ce4";
        }
    }

    [judul, isi, kategori, tanggal].forEach(function (element) {
        element.addEventListener("input", updatePreview);
        element.addEventListener("change", updatePreview);
    });

    updatePreview();
</script>

</body>
</html>
