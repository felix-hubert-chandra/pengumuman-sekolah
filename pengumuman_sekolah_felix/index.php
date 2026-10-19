
<?php
require_once "koneksi.php";

$query = $pdo->prepare(
    "SELECT * FROM pengumuman_sekolah
     ORDER BY id_pengumuman DESC"
);
$query->execute();
$data = $query->fetchAll(PDO::FETCH_ASSOC);

$total = count($data);
$akademik = 0;
$ekstrakurikuler = 0;
$umum = 0;

foreach ($data as $row) {
    if ($row["kategori"] === "Akademik") {
        $akademik++;
    } elseif ($row["kategori"] === "Ekstrakurikuler") {
        $ekstrakurikuler++;
    } elseif ($row["kategori"] === "Umum") {
        $umum++;
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard | EduBoard</title>

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

        .page-wrap {
            max-width: 1150px;
            margin: auto;
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
            box-shadow: 0 5px 18px rgba(82,109,255,.25);
        }

        .nav-label {
            color: #aab6d5;
            font-size: 11px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 35px;
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
            max-width: 530px;
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

        .btn-primary-custom {
            display: inline-block;
            background: linear-gradient(120deg, #526dff, #4055e5);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 13px 18px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 7px 16px rgba(82,109,255,.22);
            transition: .2s;
        }

        .btn-primary-custom:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(82,109,255,.28);
        }

        .stat-card {
            height: 100%;
            background: white;
            border: 1px solid #edf0f7;
            border-radius: 19px;
            box-shadow: 0 8px 30px rgba(29,48,91,.04);
            transition: transform .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-label {
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .stat-number {
            font-size: 27px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .content-card {
            background: white;
            border: 1px solid #edf0f7;
            border-radius: 22px;
            box-shadow: 0 8px 35px rgba(29,48,91,.045);
            overflow: hidden;
        }

        .section-heading {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .section-subtitle {
            font-size: 11px;
            color: var(--muted);
            line-height: 1.8;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: #f7f8fc;
            color: #8994ad;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .9px;
            padding: 17px 15px;
            white-space: nowrap;
            border-bottom: 1px solid var(--line);
        }

        .table tbody td {
            padding: 18px 15px;
            border-color: #edf0f7;
            font-size: 11px;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background .2s;
        }

        .table tbody tr:hover td {
            background: #fafbff;
        }

        .row-number {
            color: #9aa5b9;
            font-weight: 700;
        }

        .announcement-title {
            font-size: 12px;
            color: #253552;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .announcement-body {
            color: #8792a8;
            font-size: 10px;
            line-height: 1.8;
            max-width: 290px;
            overflow-wrap: anywhere;
        }

        .category {
            display: inline-block;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 9px;
            font-weight: 700;
            white-space: nowrap;
        }

        .cat-akademik {
            background: #e8edff;
            color: #435ce4;
        }

        .cat-ekstra {
            background: #dff8ed;
            color: #16815b;
        }

        .cat-umum {
            background: #fff0d9;
            color: #b77512;
        }

        .date-text {
            color: #697791;
            font-size: 10px;
            white-space: nowrap;
        }

        .btn-action {
            display: inline-block;
            text-decoration: none;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 10px;
            font-weight: 700;
            transition: .2s;
        }

        .btn-edit {
            background: #fff1d7;
            color: #a96d09;
            border: 1px solid #ffe7b6;
        }

        .btn-edit:hover {
            background: #ffe4aa;
            color: #865600;
        }

        .btn-delete {
            background: #fff0f2;
            color: #d74760;
            border: 1px solid #ffdde3;
        }

        .btn-delete:hover {
            background: #ffdde3;
            color: #b52540;
        }

        .empty-state {
            padding: 48px 20px;
            text-align: center;
        }

        .empty-icon {
            width: 62px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 17px;
            border-radius: 20px;
            background: #edf1ff;
            font-size: 28px;
        }

        .empty-title {
            font-size: 15px;
            font-weight: 800;
        }

        .footer {
            color: #9aa4b7;
            text-align: center;
            font-size: 10px;
            padding: 25px 0;
            line-height: 1.9;
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

            .stat-number {
                font-size: 23px;
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

        <span class="nav-label">Portal Informasi Sekolah</span>
    </div>
</nav>

<main class="container page-wrap py-4 py-md-5">

    <!-- HERO -->
    <section class="hero mb-4">
        <div class="hero-content">
            <span class="eyebrow">✦ &nbsp; DASHBOARD INFORMASI</span>

            <h1>Informasi Sekolah,<br>Dalam Satu Tempat.</h1>

            <p class="mt-3 mb-4">
                Kelola dan pantau pengumuman sekolah dengan mudah.
                Semua informasi penting tersusun rapi agar mudah ditemukan.
            </p>

            <a href="tambah.php" class="btn-primary-custom">
                + Buat Pengumuman Baru
            </a>
        </div>

        <div class="hero-icon" aria-hidden="true">▤</div>
    </section>

    <!-- STATISTIK -->
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"
                         style="background:#e8edff;color:#435ce4;">
                        ▤
                    </div>
                    <div>
                        <div class="stat-label">Total Pengumuman</div>
                        <div class="stat-number"><?= $total ?></div>
                    </div>
                </div>
                <div class="section-subtitle mt-3">
                    Semua data yang tersimpan
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"
                         style="background:#fff0d9;color:#b77512;">
                        ✎
                    </div>
                    <div>
                        <div class="stat-label">Akademik</div>
                        <div class="stat-number"><?= $akademik ?></div>
                    </div>
                </div>
                <div class="section-subtitle mt-3">
                    Informasi pembelajaran
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"
                         style="background:#dff8ed;color:#16815b;">
                        ☆
                    </div>
                    <div>
                        <div class="stat-label">Ekstrakurikuler</div>
                        <div class="stat-number"><?= $ekstrakurikuler ?></div>
                    </div>
                </div>
                <div class="section-subtitle mt-3">
                    Aktivitas dan organisasi
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"
                         style="background:#f0e8ff;color:#8855cb;">
                        ◈
                    </div>
                    <div>
                        <div class="stat-label">Umum</div>
                        <div class="stat-number"><?= $umum ?></div>
                    </div>
                </div>
                <div class="section-subtitle mt-3">
                    Informasi lainnya
                </div>
            </div>
        </div>

    </div>

    <!-- DAFTAR PENGUMUMAN -->
    <section class="content-card">

        <div class="p-4 p-md-4 d-flex flex-wrap justify-content-between
                    align-items-center gap-3">

            <div class="d-flex align-items-center gap-3">
                <div class="brand-mark"
                     style="margin:0;width:43px;height:43px;">
                    ▤
                </div>

                <div>
                    <h2 class="section-heading mb-1">
                        Daftar Pengumuman
                    </h2>

                    <p class="section-subtitle mb-0">
                        Semua informasi yang telah diterbitkan.
                    </p>
                </div>
            </div>

            <span class="category cat-akademik">
                <?= $total ?> DATA TERSIMPAN
            </span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">No.</th>
                        <th>Informasi Pengumuman</th>
                        <th>Kategori</th>
                        <th>Tanggal Terbit</th>
                        <th style="padding-right:24px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($total > 0): ?>
                    <?php $no = 1; ?>

                    <?php foreach ($data as $row): ?>
                        <?php
                        $kategori = $row["kategori"];

                        if ($kategori === "Akademik") {
                            $catClass = "cat-akademik";
                        } elseif ($kategori === "Ekstrakurikuler") {
                            $catClass = "cat-ekstra";
                        } else {
                            $catClass = "cat-umum";
                        }
                        ?>

                        <tr>
                            <td style="padding-left:24px;">
                                <span class="row-number">
                                    <?= $no++ ?>
                                </span>
                            </td>

                            <td style="min-width:220px;">
                                <div class="announcement-title">
                                    <?= e($row["judul"]) ?>
                                </div>

                                <div class="announcement-body">
                                    <?= e($row["isi"]) ?>
                                </div>
                            </td>

                            <td>
                                <span class="category <?= $catClass ?>">
                                    <?= e($kategori) ?>
                                </span>
                            </td>

                            <td>
                                <span class="date-text">
                                    <?= e(date(
                                        "d M Y",
                                        strtotime($row["tanggal_terbit"])
                                    )) ?>
                                </span>
                            </td>

                            <td class="text-nowrap"
                                style="padding-right:24px;">
                                <a href="edit.php?id=<?= (int)$row["id_pengumuman"] ?>"
                                   class="btn-action btn-edit">
                                    ✎ Edit
                                </a>

                                <a href="hapus.php?id=<?= (int)$row["id_pengumuman"] ?>"
                                   class="btn-action btn-delete"
                                   onclick="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                    × Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon">▤</div>

                                <div class="empty-title mb-2">
                                    Belum Ada Pengumuman
                                </div>

                                <p class="section-subtitle mb-4">
                                    Pengumuman yang kamu tambahkan akan
                                    muncul di halaman ini.
                                </p>

                                <a href="tambah.php"
                                   class="btn-primary-custom">
                                    + Buat Pengumuman Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between gap-2
                    align-items-center p-3 px-4"
             style="border-top:1px solid #edf0f7;">
            <span class="section-subtitle">
                Data diambil langsung dari database MySQL.
            </span>

            <span class="section-subtitle">
                Total: <strong><?= $total ?></strong> pengumuman
            </span>
        </div>

    </section>

    <footer class="footer">
        <strong style="color:#697791;">EduBoard</strong> · Portal Informasi Sekolah
        <br>
        Dibangun dengan PHP Native, PDO, MySQL & Bootstrap
    </footer>

</main>

</body>
</html>

