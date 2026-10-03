<!-- views/dashboard.php -->
<div class="row">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Dashboard Utama</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Selamat datang kembali di Panel Administrasi Kehadiran & Penggajian Karyawan</p>
            </div>
            <div class="badge bg-accent-soft text-accent-primary border p-2" style="font-size: 0.85rem; font-weight: 600;">
                <i class="fa-solid fa-calendar-days me-1"></i> Running: Periode <?= $periode_ke ?> (<?= date('M Y') ?>)
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards Grid for Admin Utama (Full Metrics) -->
<?php if (is_admin_utama()): ?>
<div class="row g-3 mb-4">
    <!-- Total Karyawan -->
    <div class="col-xl-3 col-sm-6">
        <div class="card-stat stat-primary">
            <div class="stat-info">
                <h6>Total Karyawan</h6>
                <h3><?= $total_karyawan ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Hadir Hari Ini -->
    <div class="col-xl-3 col-sm-6">
        <div class="card-stat stat-success">
            <div class="stat-info">
                <h6>Hadir Hari Ini</h6>
                <h3><?= $stats_today['hadir'] ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
    </div>

    <!-- Setengah Hari Hari Ini -->
    <div class="col-xl-3 col-sm-6">
        <div class="card-stat stat-info">
            <div class="stat-info">
                <h6>Setengah Hari</h6>
                <h3><?= $stats_today['setengah_hari'] ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>
    </div>

    <!-- Izin Hari Ini -->
    <div class="col-xl-3 col-sm-6">
        <div class="card-stat stat-warning">
            <div class="stat-info">
                <h6>Izin Hari Ini</h6>
                <h3><?= $stats_today['izin'] ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-minus"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Lembur Periode Berjalan -->
    <div class="col-xl-4 col-sm-6">
        <div class="card-stat stat-success">
            <div class="stat-info">
                <h6>Lembur Periode Ini</h6>
                <h3 style="font-size: 1.4rem;"><?= format_rupiah($total_lembur) ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>

    <!-- Total Piutang Aktif -->
    <div class="col-xl-4 col-sm-6">
        <div class="card-stat stat-danger">
            <div class="stat-info">
                <h6>Piutang Aktif</h6>
                <h3 style="font-size: 1.4rem;"><?= format_rupiah($total_piutang) ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>

    <!-- Estimasi Gaji Periode Berjalan -->
    <div class="col-xl-4 col-sm-6">
        <div class="card-stat stat-primary">
            <div class="stat-info">
                <h6>Estimasi Gaji Bersih</h6>
                <h3 style="font-size: 1.4rem;"><?= format_rupiah($estimasi_gaji) ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards Grid for Admin Divisi (Filtered Attendance Metrics Only) -->
<?php else: ?>
<div class="row g-3 mb-4">
    <!-- Total Karyawan Divisi -->
    <div class="col-md-3 col-sm-6">
        <div class="card-stat stat-primary h-100">
            <div class="stat-info">
                <h6>Karyawan Divisi</h6>
                <h3><?= $total_karyawan ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Hadir Hari Ini -->
    <div class="col-md-3 col-sm-6">
        <div class="card-stat stat-success h-100">
            <div class="stat-info">
                <h6>Hadir Hari Ini</h6>
                <h3><?= $stats_today['hadir'] ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
    </div>

    <!-- Setengah Hari Hari Ini -->
    <div class="col-md-3 col-sm-6">
        <div class="card-stat stat-info h-100">
            <div class="stat-info">
                <h6>Setengah Hari</h6>
                <h3><?= $stats_today['setengah_hari'] ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>
    </div>

    <!-- Izin Hari Ini -->
    <div class="col-md-3 col-sm-6">
        <div class="card-stat stat-warning h-100">
            <div class="stat-info">
                <h6>Izin Hari Ini</h6>
                <h3><?= $stats_today['izin'] ?></h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-minus"></i>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Charts Section -->
<div class="row">
    <!-- Trend Kehadiran (Full width for Admin Divisi, 7/12 width for Admin Utama) -->
    <div class="<?= is_admin_utama() ? 'col-lg-7' : 'col-12' ?> mb-4">
        <div class="card card-premium h-100">
            <div class="card-header">
                <h5 class="mb-0" style="font-size: 1.05rem; font-weight: 600; color: var(--accent-primary);">Trend Absensi Harian (15 Hari Terakhir)</h5>
            </div>
            <div class="card-body">
                <canvas id="chart-attendance" style="max-height: 320px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Distribusi Gaji per Divisi (Only for Admin Utama) -->
    <?php if (is_admin_utama()): ?>
    <div class="col-lg-5 mb-4">
        <div class="card card-premium h-100">
            <div class="card-header">
                <h5 class="mb-0" style="font-size: 1.05rem; font-weight: 600; color: var(--accent-primary);">Distribusi Biaya Gaji per Divisi</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="w-100" style="max-width: 280px;">
                    <canvas id="chart-divisi" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Chart.js Config Scripts -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. Attendance Trend Chart ---
        const attendanceData = <?= json_encode($attendance_trend) ?>;
        
        const labels = attendanceData.map(d => {
            // format YYYY-MM-DD to DD/MM
            const dateObj = new Date(d.tanggal);
            return dateObj.getDate() + '/' + (dateObj.getMonth() + 1);
        });
        
        const hadirSeries = attendanceData.map(d => parseInt(d.hadir));
        const setengahSeries = attendanceData.map(d => parseInt(d.setengah_hari));
        const izinSeries = attendanceData.map(d => parseInt(d.izin));

        const ctxAttendance = document.getElementById('chart-attendance').getContext('2d');
        new Chart(ctxAttendance, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Hadir',
                        data: hadirSeries,
                        backgroundColor: '#2e7d32',
                    },
                    {
                        label: '½ Hari',
                        data: setengahSeries,
                        backgroundColor: '#0277bd',
                    },
                    {
                        label: 'Izin',
                        data: izinSeries,
                        backgroundColor: '#ef6c00',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { family: 'Outfit' } }
                    }
                }
            }
        });

        // --- 2. Gaji Expenses by Divisi Chart (Only initialized for Admin Utama) ---
        <?php if (is_admin_utama()): ?>
        const divisiData = <?= json_encode($expenses_by_divisi) ?>;
        
        const divisiLabels = divisiData.map(d => d.nama_divisi);
        const divisiValues = divisiData.map(d => parseFloat(d.total_gaji));
        
        // Custom elegant red-matching palette
        const colorPalette = [
            '#dc2626', // Accent Red
            '#f87171', // Soft Red
            '#fca5a5', // Lighter Red
            '#4b5563', // Dark Slate
            '#9ca3af', // Cool Gray
            '#e5e7eb'  // Light Gray
        ];

        const ctxDivisi = document.getElementById('chart-divisi').getContext('2d');
        
        if (divisiValues.length === 0) {
            // Draw empty placeholder text
            ctxDivisi.font = "14px Outfit";
            ctxDivisi.fillStyle = "#7c7267";
            ctxDivisi.textAlign = "center";
            ctxDivisi.fillText("Belum ada rekap gaji tersimpan", ctxDivisi.canvas.width/2, ctxDivisi.canvas.height/2);
        } else {
            new Chart(ctxDivisi, {
                type: 'doughnut',
                data: {
                    labels: divisiLabels,
                    datasets: [{
                        data: divisiValues,
                        backgroundColor: colorPalette.slice(0, divisiValues.length),
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                font: { family: 'Outfit', size: 10 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        // format rupiah
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
        <?php endif; ?>
    });
</script>
