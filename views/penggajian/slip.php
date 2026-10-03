<?php
// Simple helper to convert number to Indonesian words
if (!function_exists('terbilang')) {
    function terbilang($angka) {
        $angka = abs((int)$angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($angka < 12) {
            $temp = " " . $baca[$angka];
        } else if ($angka < 20) {
            $temp = terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $temp = terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
        } else if ($angka < 200) {
            $temp = " seratus" . terbilang($angka - 100);
        } else if ($angka < 1000) {
            $temp = terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
        } else if ($angka < 2000) {
            $temp = " seribu" . terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $temp = terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $temp = terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
        }
        return trim($temp);
    }
}

if (!function_exists('format_accounting')) {
    function format_accounting($val) {
        if (empty($val) || $val == 0) {
            return '-';
        }
        return number_format($val, 2, '.', ',');
    }
}

// Fetch detail potongan lainnya for this employee
require_once dirname(dirname(__DIR__)) . '/models/PotonganLainnya.php';
$potonganLainnyaModel = new PotonganLainnya();
$detail_potongan = $potonganLainnyaModel->getActiveForEmployee($payroll['id_karyawan']);

$nominal_arisan = 0;
$nominal_potongan_lainnya = 0;

foreach ($detail_potongan as $dp) {
    if (stripos($dp['nama_potongan'], 'arisan') !== false) {
        $nominal_arisan += $dp['nominal'];
    } else {
        $nominal_potongan_lainnya += $dp['nominal'];
    }
}

// Fallback check: if the employee has no active potongan mappings, but the payroll record has $payroll['potongan_lainnya'] > 0
if (empty($detail_potongan) && $payroll['potongan_lainnya'] > 0) {
    $nominal_arisan = $payroll['potongan_lainnya'];
}

// Fetch overtime hours for this period
require_once dirname(dirname(__DIR__)) . '/models/Lembur.php';
$lemburModel = new Lembur();

$start_day = ($payroll['periode_ke'] == 1) ? 1 : 16;
$end_day = ($payroll['periode_ke'] == 1) ? 15 : date('t', strtotime("{$payroll['periode_tahun']}-{$payroll['periode_bulan']}-01"));
$start_date = sprintf("%04d-%02d-%02d", $payroll['periode_tahun'], $payroll['periode_bulan'], $start_day);
$end_date = sprintf("%04d-%02d-%02d", $payroll['periode_tahun'], $payroll['periode_bulan'], $end_day);

$lemb = $lemburModel->getSumByPeriod($payroll['id_karyawan'], $start_date, $end_date);
$total_jam_lembur = $lemb['total_jam'] ?? 0;

$periode_str = "$start_day-$end_day " . format_bulan($payroll['periode_bulan']) . " " . $payroll['periode_tahun'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Slip Gaji - <?= htmlspecialchars($payroll['nama_lengkap']) ?> - <?= $periode_str ?></title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <style>
        body {
            font-family: 'Outfit', 'Inter', Arial, sans-serif;
            color: #222;
            padding: 18px;
            background: #fff;
        }
        .wrap {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .company {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .company img {
            height: 60px;
            width: 60px;
            object-fit: contain;
        }
        .company .name {
            font-weight: 700;
            color: #c62828;
            font-size: 15px;
            line-height: 1.2;
        }
        .company .subname {
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }
        .meta {
            font-size: 12px;
            min-width: 220px;
        }
        .meta table {
            width: 100%;
        }
        .meta td {
            padding: 2px 0;
            vertical-align: top;
        }
        .meta .label {
            color: #c62828;
            font-weight: 700;
            width: 80px;
        }
        .title {
            color: #c62828;
            font-weight: 700;
            margin: 15px 0 25px 0;
            font-size: 22px;
            text-align: center;
        }
        .columns-container {
            display: flex;
            gap: 30px;
        }
        .column {
            flex: 1;
        }
        .column-title {
            font-weight: 700;
            color: #c62828;
            margin-bottom: 8px;
            font-size: 13px;
            border-bottom: 1.5px solid #c62828;
            padding-bottom: 3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .data-table td {
            padding: 4px 0;
            vertical-align: middle;
        }
        .total-row {
            font-weight: 700;
            border-top: 1px solid #000;
        }
        .total-row td {
            padding-top: 6px;
        }
        .net-salary-section {
            margin-top: 25px;
            font-weight: 700;
            font-size: 15px;
            color: #c62828;
            border-top: 1px dashed #ccc;
            padding-top: 15px;
        }
        .net-salary-section .label {
            display: inline-block;
            width: 120px;
        }
        .sign-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            font-size: 12px;
        }
        .no-print {
            margin-bottom: 12px;
            text-align: center;
        }
        .no-print button {
            padding: 8px 14px;
            background: #c62828;
            color: #fff;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 5mm; }
            .wrap { border: none; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()">Cetak Slip</button>
    </div>

    <div class="wrap">
        <div class="header">
            <div class="company">
                <img src="assets/images/logo.png" alt="logo">
                <div>
                    <div class="name">Jawara Jamur</div>
                    <div class="subname">CV. Kaysan Jawara Jamur</div>
                </div>
            </div>
            <div class="meta">
                <table>
                    <tr>
                        <td class="label">Nama</td>
                        <td style="width:10px;">:</td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($payroll['nama_lengkap']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Pekerjaan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($payroll['nama_jabatan']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Periode</td>
                        <td>:</td>
                        <td><?= $periode_str ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="title">Slip Gaji</div>

        <div class="columns-container">
            <!-- PENERIMAAN -->
            <div class="column">
                <div class="column-title">Penerimaan</div>
                <table class="data-table">
                    <tr>
                        <td style="width:130px;">Gaji Kerja (Kumbung)</td>
                        <td style="width:10px;">:</td>
                        <td style="width:55px; text-align:right;"><?= number_format($payroll['total_hadir'], 1) ?> hari</td>
                        <td style="width:30px; text-align:center; color:#888;">IDR</td>
                        <td style="width:80px; text-align:right;"><?= format_accounting($payroll['gaji_harian']) ?></td>
                        <td style="width:30px; text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right; font-weight:600;"><?= format_accounting($payroll['gaji_pokok']) ?></td>
                    </tr>
                    <tr>
                        <td>Gaji Kerja (Proyek)</td>
                        <td>:</td>
                        <td style="text-align:right;"><?= (int)($payroll['total_kerja_proyek'] ?? 0) ?> hari</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right;">150,000.00</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right; font-weight:600;"><?= format_accounting($payroll['gaji_proyek']) ?></td>
                    </tr>
                    <tr>
                        <td>Lainnya</td>
                        <td>:</td>
                        <td style="text-align:right;">hari</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right;">200,000.00</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right; font-weight:600;">-</td>
                    </tr>
                    <tr>
                        <td>Lembur (Kumbung)</td>
                        <td>:</td>
                        <td style="text-align:right;"><?= number_format($total_jam_lembur, 1) ?> jam</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right;"><?= format_accounting($payroll['tarif_lembur'] ?? 10000) ?></td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right; font-weight:600;"><?= format_accounting($payroll['total_lembur']) ?></td>
                    </tr>
                    <tr>
                        <td>Lembur (Proyek)</td>
                        <td>:</td>
                        <td style="text-align:right;">0 jam</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right;">15,000.00</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right; font-weight:600;">-</td>
                    </tr>
                    <tr>
                        <td>Bonus</td>
                        <td>:</td>
                        <td colspan="5"></td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Penerimaan</td>
                        <td>:</td>
                        <td colspan="3"></td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right;"><?= format_accounting(($payroll['gaji_pokok'] ?? 0) + ($payroll['gaji_proyek'] ?? 0) + ($payroll['total_lembur'] ?? 0)) ?></td>
                    </tr>
                </table>
            </div>

            <!-- POTONGAN -->
            <div class="column" style="max-width:320px;">
                <div class="column-title">Potongan</div>
                <table class="data-table">
                    <tr>
                        <td style="width:110px;">Arisan</td>
                        <td style="width:10px;">:</td>
                        <td style="width:30px; text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right; font-weight:600;"><?= format_accounting($nominal_arisan) ?></td>
                    </tr>
                    <tr>
                        <td>Pinjaman</td>
                        <td>:</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right; font-weight:600;"><?= format_accounting($payroll['total_piutang']) ?></td>
                    </tr>
                    <tr>
                        <td>Lainnya</td>
                        <td>:</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right; font-weight:600;"><?= format_accounting($nominal_potongan_lainnya) ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Potongan</td>
                        <td>:</td>
                        <td style="text-align:center; color:#888;">IDR</td>
                        <td style="text-align:right;"><?= format_accounting($nominal_arisan + $payroll['total_piutang'] + $nominal_potongan_lainnya) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="net-salary-section">
            <span class="label">Gaji Bersih</span>
            <span>: IDR</span>
            <span style="margin-left:70px; font-size:17px; font-weight:bold;"><?= format_accounting($payroll['gaji_bersih']) ?></span>
        </div>

        <div class="sign-section">
            <div>
                <p style="margin-bottom:50px;">Penerima,</p>
                <p><strong><?= htmlspecialchars($payroll['nama_lengkap']) ?></strong></p>
            </div>
            <div style="text-align:right;">
                <p style="margin-bottom:50px;">Bagian Keuangan,</p>
                <p><strong>Ovi Mariana Sari</strong></p>
            </div>
        </div>
    </div>

</body>
</html>
