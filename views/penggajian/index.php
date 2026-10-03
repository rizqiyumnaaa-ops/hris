<!-- Penggajian Management View -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Penggajian Karyawan (Payroll)</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola slip gaji bulanan berdasarkan dua periode pembayaran</p>
            </div>
        </div>
    </div>
</div>

<!-- Period Selection Card -->
<div class="card card-premium mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="penggajian">
            
            <div class="col-md-3">
                <label for="tahun" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Tahun</label>
                <select class="form-select shadow-none" id="tahun" name="tahun">
                    <?php 
                    $current_yr = (int)date('Y');
                    for ($y = $current_yr - 3; $y <= $current_yr + 1; $y++): 
                    ?>
                    <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="bulan" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Bulan</label>
                <select class="form-select shadow-none" id="bulan" name="bulan">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $bulan == $m ? 'selected' : '' ?>><?= format_bulan($m) ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="periode_ke" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Periode Pembayaran</label>
                <select class="form-select shadow-none" id="periode_ke" name="periode_ke">
                    <option value="1" <?= $periode_ke == 1 ? 'selected' : '' ?>>Periode I (Tanggal 1 - 15)</option>
                    <option value="2" <?= $periode_ke == 2 ? 'selected' : '' ?>>Periode II (Tanggal 16 - Akhir Bulan)</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-accent w-100 py-2"><i class="fa-solid fa-magnifying-glass me-2"></i> Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabs Card -->
<div class="row">
    <div class="col-12">
        <ul class="nav nav-pills mb-4 gap-2" id="payroll-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="saved-tab" data-bs-toggle="pill" data-bs-target="#saved-pane" type="button" role="tab" aria-controls="saved-pane" aria-selected="true" style="border-radius: 8px;">
                    <i class="fa-solid fa-file-invoice-dollar me-2"></i> Gaji Disimpan (<?= count($saved_payrolls) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="pill" data-bs-target="#pending-pane" type="button" role="tab" aria-controls="pending-pane" aria-selected="false" style="border-radius: 8px;">
                    <i class="fa-solid fa-circle-pause me-2"></i> Belum Diproses (<?= count($pending_employees) ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="payroll-tabs-content">
            <!-- Saved Payroll Pane -->
            <div class="tab-pane fade show active" id="saved-pane" role="tabpanel" aria-labelledby="saved-tab" tabindex="0">
                <form id="form-saved-payrolls" action="index.php?page=penggajian&action=pdf_selected" method="POST">
                    <input type="hidden" name="tahun" value="<?= $tahun ?>">
                    <input type="hidden" name="bulan" value="<?= $bulan ?>">
                    <input type="hidden" name="periode_ke" value="<?= $periode_ke ?>">
                    <div class="card card-premium">
                        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                            <h5 class="mb-0 text-accent-primary" style="font-weight: 600;">Daftar Slip Gaji Tersimpan</h5>
                            <?php if (!empty($saved_payrolls)): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" formaction="index.php?page=penggajian&action=pdf_selected" formtarget="_blank" class="btn btn-sm btn-danger d-flex align-items-center gap-2" title="Unduh PDF dari slip gaji yang dicentang">
                                    <i class="fa-solid fa-file-pdf"></i> Unduh PDF Terpilih
                                </button>
                                <a href="index.php?page=penggajian&action=excel&tahun=<?= $tahun ?>&bulan=<?= $bulan ?>&periode_ke=<?= $periode_ke ?>" class="btn btn-sm btn-success d-flex align-items-center gap-2" style="background-color: #2e7d32; border: none;">
                                    <i class="fa-solid fa-file-excel"></i> Export Excel
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (empty($saved_payrolls)): ?>
                                <div class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486831.png" alt="Empty" style="width: 80px;" class="mb-3 opacity-50">
                                    <h5 class="text-muted">Belum Ada Gaji Tersimpan</h5>
                                    <p class="text-muted mb-0">Silakan buka tab "Belum Diproses" untuk mengkalkulasi dan menyimpan gaji periode ini.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-premium datatable w-100" data-searching="false" data-length-change="false">
                                        <thead>
                                            <tr>
                                                <th width="3%" class="text-center">
                                                    <input type="checkbox" id="check-all-saved" class="form-check-input">
                                                </th>
                                                <th width="4%">No</th>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Divisi / Jabatan</th>
                                                <th>Gaji Pokok</th>
                                                <th>Gaji Proyek (+)</th>
                                                <th>Lembur (+)</th>
                                                <th>Piutang (-)</th>
                                                <th>Potongan Lain (-)</th>
                                                <th>Gaji Bersih</th>
                                                <th width="12%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $no = 1;
                                            foreach ($saved_payrolls as $row): 
                                                $total_potongan = $row['total_piutang'];
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="payrolls[]" value="<?= $row['id'] ?>" class="form-check-input check-saved-item">
                                                </td>
                                                <td><?= $no++ ?></td>
                                                <td><strong><?= $row['nik'] ?></strong></td>
                                                <td><?= $row['nama_lengkap'] ?></td>
                                                <td>
                                                    <div class="d-flex flex-column align-items-start gap-1">
                                                        <span class="badge-divisi">
                                                            <i class="fa-solid fa-building me-1 opacity-75"></i><?= htmlspecialchars($row['nama_divisi'] ?? '') ?>
                                                        </span>
                                                        <span class="badge-jabatan">
                                                            <i class="fa-solid fa-user-tag me-1 opacity-75"></i><?= htmlspecialchars($row['nama_jabatan'] ?? '') ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td><?= format_rupiah($row['gaji_pokok']) ?></td>
                                                <td><span class="text-accent-primary">+<?= format_rupiah($row['gaji_proyek'] ?? 0) ?></span></td>
                                                <td><span class="text-success">+<?= format_rupiah($row['total_lembur']) ?></span></td>
                                                <td><span class="text-danger">-<?= format_rupiah($total_potongan) ?></span></td>
                                                <td>
                                                    <?php if (!empty($row['potongan_lainnya']) && $row['potongan_lainnya'] > 0): ?>
                                                        <span class="text-danger">-<?= format_rupiah($row['potongan_lainnya']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><strong class="text-accent-primary"><?= format_rupiah($row['gaji_bersih']) ?></strong></td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="index.php?page=penggajian&action=slip&id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-outline-accent d-inline-flex align-items-center">
                                                            <i class="fa-solid fa-print me-1"></i> Slip
                                                        </a>
                                                        <a href="index.php?page=penggajian&action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete-confirm btn-icon">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Unprocessed Payroll Pane -->
            <div class="tab-pane fade" id="pending-pane" role="tabpanel" aria-labelledby="pending-tab" tabindex="0">
                <form action="index.php?page=penggajian&action=simpan" method="POST" id="form-proses-gaji">
                    <input type="hidden" name="tahun" value="<?= $tahun ?>">
                    <input type="hidden" name="bulan" value="<?= $bulan ?>">
                    <input type="hidden" name="periode_ke" value="<?= $periode_ke ?>">
                    
                    <div class="card card-premium">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-accent-primary" style="font-weight: 600;">Kalkulasi Gaji Karyawan Aktif</h5>
                            <?php if (!empty($pending_employees)): ?>
                            <button type="submit" class="btn btn-accent d-flex align-items-center gap-2">
                                <i class="fa-solid fa-calculator"></i> Simpan Gaji Terpilih (Bulk)
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (empty($pending_employees)): ?>
                                <div class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4436/4436481.png" alt="Done" style="width: 80px;" class="mb-3 opacity-75">
                                    <h5 class="text-success">Semua Gaji Karyawan Telah Diproses</h5>
                                    <p class="text-muted mb-0">Tidak ada data karyawan aktif tertunda untuk periode terpilih ini.</p>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info py-2" style="font-size: 0.85rem; border-radius: 8px;">
                                    <i class="fa-solid fa-info-circle me-1"></i> Centang kotak di kolom paling kiri lalu klik tombol <strong>Simpan Gaji Terpilih</strong> untuk menyimpan slip gaji secara permanen.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-premium w-100 align-middle">
                                        <thead>
                                            <tr>
                                                <th width="3%" class="text-center">
                                                    <input type="checkbox" id="check-all" class="form-check-input">
                                                </th>
                                                <th>Nama Karyawan</th>
                                                <th>Hari Kerja / Hadir / Proyek</th>
                                                <th>Gaji Pokok (Reguler)</th>
                                                <th>Gaji Proyek</th>
                                                <th>Lembur (+)</th>
                                                <th>Piutang (-)</th>
                                                <th>Potongan Lain (-)</th>
                                                <th>Estimasi Gaji Bersih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pending_employees as $p): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="employees[]" value="<?= $p['id_karyawan'] ?>" class="form-check-input check-item">
                                                </td>
                                                <td>
                                                    <strong><?= $p['nama_lengkap'] ?></strong>
                                                    <span class="d-block text-muted" style="font-size: 0.75rem;">NIK: <?= $p['nik'] ?></span>
                                                </td>
                                                <td>
                                                    <strong><?= $p['jumlah_hari_kerja'] ?> hari</strong>
                                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Hadir: <?= number_format($p['total_hadir'], 1) ?> | Proyek: <?= $p['total_kerja_proyek'] ?></span>
                                                </td>
                                                <td><?= format_rupiah($p['gaji_pokok']) ?></td>
                                                <td>
                                                    <span class="text-accent-primary"><?= format_rupiah($p['gaji_proyek']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-success"><?= format_rupiah($p['total_lembur']) ?></span>
                                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Lembur: <?= $p['total_jam_lembur'] ?> jam</span>
                                                </td>
                                                <td>
                                                    <span class="text-danger"><?= format_rupiah($p['total_piutang']) ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($p['potongan_lainnya']) && $p['potongan_lainnya'] > 0): ?>
                                                        <span class="text-danger">-<?= format_rupiah($p['potongan_lainnya']) ?></span>
                                                        <?php if (!empty($p['detail_potongan_lainnya'])): ?>
                                                        <span class="d-block text-muted" style="font-size: 0.7rem;">
                                                            <?php foreach($p['detail_potongan_lainnya'] as $det): ?>
                                                                <?= htmlspecialchars($det['nama_potongan']) ?>
                                                            <?php endforeach; ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong class="text-accent-primary" style="font-size: 1.05rem;"><?= format_rupiah($p['gaji_bersih']) ?></strong>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Select All for Unprocessed Payrolls
        const checkAll = document.getElementById('check-all');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const items = document.querySelectorAll('.check-item');
                items.forEach(item => {
                    item.checked = this.checked;
                });
            });
        }

        // Toggle Select All for Saved Payrolls
        const checkAllSaved = document.getElementById('check-all-saved');
        if (checkAllSaved) {
            checkAllSaved.addEventListener('change', function() {
                const items = document.querySelectorAll('.check-saved-item');
                items.forEach(item => {
                    item.checked = this.checked;
                });
            });
        }

        // Validate selection before submitting form-saved-payrolls
        const formSaved = document.getElementById('form-saved-payrolls');
        if (formSaved) {
            formSaved.addEventListener('submit', function(e) {
                const checkedItems = document.querySelectorAll('.check-saved-item:checked');
                if (checkedItems.length === 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih Slip Gaji',
                            text: 'Harap centang setidaknya satu slip gaji yang ingin diproses!'
                        });
                    } else {
                        alert('Harap centang setidaknya satu slip gaji yang ingin diproses!');
                    }
                }
            });
        }

        // Show spinner on form process submit
        const formGaji = document.getElementById('form-proses-gaji');
        if (formGaji) {
            formGaji.addEventListener('submit', function() {
                $('#loader-overlay').fadeIn(100);
            });
        }
    });
</script>
