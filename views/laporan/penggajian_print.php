<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Payroll - Bulan_<?= $filters['bulan'] ?>_Tahun_<?= $filters['tahun'] ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            font-family: sans-serif;
            color: #000;
            font-size: 10pt;
            padding: 30px;
        }
        .print-header {
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .table-print {
            width: 100%;
            border-collapse: collapse;
        }
        .table-print th {
            background-color: #f2f2f2 !important;
            border: 1px solid #333 !important;
            padding: 8px !important;
            font-weight: bold;
            font-size: 8pt;
            text-align: left;
        }
        .table-print td {
            border: 1px solid #333 !important;
            padding: 8px !important;
            font-size: 8pt;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Print Floating Buttons -->
    <div class="container text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-dark px-4"><i class="fa-solid fa-print"></i> Cetak Ulang</button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-4 ms-2">Tutup</button>
    </div>

    <!-- Printable Area -->
    <div class="print-header">
        <div class="row align-items-center">
            <div class="col-8">
                <h3 class="mb-0 font-weight-bold">HRIS PENGGAJIAN & PRESENSI</h3>
                <small class="text-muted">Rekapitulasi Payout Payroll Karyawan</small>
            </div>
            <div class="col-4 text-end">
                <small>
                    Dicetak: <?= date('d/m/Y H:i') ?><br>
                    Tahun / Bulan: <?= $filters['tahun'] ?> / <?= format_bulan($filters['bulan']) ?>
                </small>
            </div>
        </div>
    </div>

    <h4 class="text-center mb-4">LAPORAN REKAPITULASI PENGGAJIAN</h4>

    <table class="table-print table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Divisi</th>
                <th>Jabatan</th>
                <th width="8%">Periode</th>
                <th>Gaji Pokok</th>
                <th>Lembur</th>
                <th>Pot. ½ Hari</th>
                <th>Pot. Alpha</th>
                <th>Piutang</th>
                <th>Pot. Lainnya</th>
                <th>Gaji Bersih</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $grand_total = 0;
            if (empty($data)):
            ?>
            <tr>
                <td colspan="12" class="text-center">Tidak ada data penggajian pada periode ini.</td>
            </tr>
            <?php 
            else:
                foreach ($data as $row): 
                    $grand_total += $row['gaji_bersih'];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nik'] ?></td>
                <td><strong><?= $row['nama_lengkap'] ?></strong></td>
                <td><?= $row['nama_divisi'] ?></td>
                <td><?= $row['nama_jabatan'] ?></td>
                <td>Periode <?= $row['periode_ke'] ?></td>
                <td><?= format_rupiah($row['gaji_pokok']) ?></td>
                <td><?= format_rupiah($row['total_lembur']) ?></td>
                <td><?= format_rupiah($row['potongan_setengah_hari']) ?></td>
                <td><?= format_rupiah($row['potongan_alpha']) ?></td>
                <td><?= format_rupiah($row['total_piutang']) ?></td>
                <td><?= format_rupiah($row['potongan_lainnya'] ?? 0) ?></td>
                <td><strong><?= format_rupiah($row['gaji_bersih']) ?></strong></td>
            </tr>
            <?php 
                endforeach; 
            ?>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="12" class="text-end">TOTAL GAJI BERSIH DIBAYARKAN:</td>
                <td><?= format_rupiah($grand_total) ?></td>
            </tr>
            <?php
            endif;
            ?>
        </tbody>
    </table>

    <div class="row mt-5">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p class="mb-5">Mengetahui,<br>HR Manager</p>
            <p class="border-top pt-2 d-inline-block px-4"><strong>Administrator</strong></p>
        </div>
    </div>

</body>
</html>
