<?php
// Fungsi terbilang harus didefinisikan sebelum dipanggil
if (!function_exists('terbilang')) {
    function terbilang($angka) {
        $angka = abs((int)$angka);
        $baca  = ["","satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan","sepuluh","sebelas"];
        $temp  = "";
        if ($angka < 12)          { $temp = " " . $baca[$angka]; }
        elseif ($angka < 20)      { $temp = terbilang($angka - 10) . " belas"; }
        elseif ($angka < 100)     { $temp = terbilang((int)($angka / 10)) . " puluh" . terbilang($angka % 10); }
        elseif ($angka < 200)     { $temp = " seratus" . terbilang($angka - 100); }
        elseif ($angka < 1000)    { $temp = terbilang((int)($angka / 100)) . " ratus" . terbilang($angka % 100); }
        elseif ($angka < 2000)    { $temp = " seribu" . terbilang($angka - 1000); }
        elseif ($angka < 1000000) { $temp = terbilang((int)($angka / 1000)) . " ribu" . terbilang($angka % 1000); }
        else                      { $temp = terbilang((int)($angka / 1000000)) . " juta" . terbilang($angka % 1000000); }
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

require_once dirname(dirname(__DIR__)) . '/models/PotonganLainnya.php';
require_once dirname(dirname(__DIR__)) . '/models/Lembur.php';

$potonganLainnyaModel = new PotonganLainnya();
$lemburModel = new Lembur();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua Slip Gaji - Periode <?= $periode_ke ?> - <?= format_bulan($bulan) ?> <?= $tahun ?></title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Outfit', 'Inter', Arial, sans-serif;
            color: #333333;
            font-size: 10pt;
            padding: 20px;
        }
        .print-slip-page {
            page-break-after: always;
            border: 2px dashed #c62828;
            padding: 25px;
            max-width: 800px;
            margin: 0 auto 40px auto;
            background-color: #fff;
        }
        .print-slip-page:last-child {
            page-break-after: avoid;
            margin-bottom: 0;
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
            text-align: left;
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
            margin-bottom: 20px;
            text-align: center;
        }
        @media print {
            body {
                padding: 0;
                background: #fff;
            }
            .print-slip-page {
                border: none;
                padding: 5px;
                max-width: 100%;
                margin-bottom: 0;
                page-break-after: always;
            }
            .print-slip-page:last-child {
                page-break-after: avoid;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Actions Panel -->
    <div class="container text-center mb-4 no-print">
        <button onclick="downloadIndividualPDFs()" class="btn btn-danger px-4 py-2 me-2" style="background-color:#c62828; border-color:#c62828; border-radius: 8px;">
            <i class="fa-solid fa-file-pdf me-2"></i> Unduh PDF Per Slip (1 File / Slip)
        </button>
        <button onclick="downloadCombinedPDF()" class="btn btn-outline-danger px-4 py-2 me-2" style="border-radius: 8px;">
            <i class="fa-solid fa-file-pdf me-2"></i> Unduh 1 File Gabungan
        </button>
        <button onclick="window.print()" class="btn btn-outline-secondary px-4 py-2 me-2" style="border-radius: 8px;">
            <i class="fa-solid fa-print me-2"></i> Cetak / Simpan via Browser
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px;">
            Tutup Halaman
        </button>
    </div>

    <div id="slips-container">
    <!-- Loop Slips -->
    <?php foreach ($payrolls as $payroll): 
        // Fetch detail potongan lainnya for this employee
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
        
        // Fallback check
        if (empty($detail_potongan) && $payroll['potongan_lainnya'] > 0) {
            $nominal_arisan = $payroll['potongan_lainnya'];
        }
        
        // Fetch overtime hours
        $start_day = ($payroll['periode_ke'] == 1) ? 1 : 16;
        $end_day = ($payroll['periode_ke'] == 1) ? 15 : date('t', strtotime("{$payroll['periode_tahun']}-{$payroll['periode_bulan']}-01"));
        $start_date = sprintf("%04d-%02d-%02d", $payroll['periode_tahun'], $payroll['periode_bulan'], $start_day);
        $end_date = sprintf("%04d-%02d-%02d", $payroll['periode_tahun'], $payroll['periode_bulan'], $end_day);
        
        $lemb = $lemburModel->getSumByPeriod($payroll['id_karyawan'], $start_date, $end_date);
        $total_jam_lembur = $lemb['total_jam'] ?? 0;
        
        $periode_str = "$start_day-$end_day " . format_bulan($payroll['periode_bulan']) . " " . $payroll['periode_tahun'];
        $nama_bersih = preg_replace('/[^A-Za-z0-9_\-\(\) ]/', '', $payroll['nama_lengkap']);
        $pdf_filename = "Slip Gaji - {$nama_bersih} - Periode {$payroll['periode_ke']} (" . format_bulan($payroll['periode_bulan']) . " {$payroll['periode_tahun']}).pdf";
    ?>
    <div class="print-slip-page" data-filename="<?= htmlspecialchars($pdf_filename) ?>">
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
    <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        async function downloadIndividualPDFs() {
            const pages = document.querySelectorAll('.print-slip-page');
            const total = pages.length;
            if (total === 0) return;

            Swal.fire({
                title: 'Mengunduh PDF Slip Gaji...',
                html: `Memproses <b id="swal-current">1</b> dari <b>${total}</b> file PDF slip gaji...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            for (let i = 0; i < pages.length; i++) {
                const page = pages[i];
                const filename = page.getAttribute('data-filename') || `Slip_Gaji_${i+1}.pdf`;

                const currentEl = document.getElementById('swal-current');
                if (currentEl) currentEl.textContent = (i + 1);

                const htmlContent = page.cloneNode(true);
                htmlContent.style.margin = '0';
                htmlContent.style.border = 'none';

                const opt = {
                    margin:       [5, 5, 5, 5],
                    filename:     filename,
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true, logging: false },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                const contentContainer = document.createElement('div');
                contentContainer.appendChild(htmlContent);

                await html2pdf().set(opt).from(contentContainer).save();
                await new Promise(resolve => setTimeout(resolve, 600));
            }

            Swal.fire({
                icon: 'success',
                title: 'Selesai!',
                text: `Berhasil mengunduh ${total} file PDF slip gaji secara terpisah!`,
                confirmButtonColor: '#c62828'
            });
        }

        function downloadCombinedPDF() {
            const element = document.getElementById('slips-container');
            const opt = {
                margin:       [5, 5, 5, 5],
                filename:     'Semua_Slip_Gaji_Gabungan.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['pdf_all', 'pdf_selected'])): ?>
                setTimeout(function() {
                    downloadIndividualPDFs();
                }, 500);
            <?php endif; ?>
        });
    </script>
</body>
</html>
