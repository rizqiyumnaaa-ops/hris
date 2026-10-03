<!-- views/laporan/penggajian.php -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Laporan Penggajian Karyawan</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Filter dan ekspor rekapitulasi penggajian karyawan periode tertentu</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-premium mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="page" value="laporan">
            <input type="hidden" name="action" value="penggajian">
            
            <div class="col-md-2">
                <label for="tahun" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Tahun</label>
                <select class="form-select shadow-none" id="tahun" name="tahun">
                    <?php 
                    $current_yr = (int)date('Y');
                    for ($y = $current_yr - 3; $y <= $current_yr + 1; $y++): 
                    ?>
                    <option value="<?= $y ?>" <?= $filters['tahun'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="bulan" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Bulan</label>
                <select class="form-select shadow-none" id="bulan" name="bulan">
                    <option value="">-- Semua Bulan --</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $filters['bulan'] == $m ? 'selected' : '' ?>><?= format_bulan($m) ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="periode_ke" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Periode Pembayaran</label>
                <select class="form-select shadow-none" id="periode_ke" name="periode_ke">
                    <option value="">-- Semua Periode --</option>
                    <option value="1" <?= $filters['periode_ke'] == 1 ? 'selected' : '' ?>>Periode I (Tanggal 1 - 15)</option>
                    <option value="2" <?= $filters['periode_ke'] == 2 ? 'selected' : '' ?>>Periode II (Tanggal 16 - Akhir Bulan)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="id_divisi" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Filter Divisi</label>
                <select class="form-select shadow-none" id="id_divisi" name="id_divisi">
                    <option value="">-- Semua Divisi --</option>
                    <?php foreach ($divisi as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $filters['id_divisi'] == $d['id'] ? 'selected' : '' ?>><?= $d['nama_divisi'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="id_jabatan" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Filter Jabatan</label>
                <select class="form-select shadow-none" id="id_jabatan" name="id_jabatan">
                    <option value="">-- Semua Jabatan --</option>
                    <?php foreach ($jabatan as $j): ?>
                    <option value="<?= $j['id'] ?>" <?= $filters['id_jabatan'] == $j['id'] ? 'selected' : '' ?>><?= $j['nama_jabatan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="id_karyawan" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Karyawan Spesifik</label>
                <select class="form-select shadow-none" id="id_karyawan" name="id_karyawan">
                    <option value="">-- Semua Karyawan --</option>
                    <?php foreach ($karyawan as $emp): ?>
                    <option value="<?= $emp['id'] ?>" <?= $filters['id_karyawan'] == $emp['id'] ? 'selected' : '' ?>><?= $emp['nik'] ?> - <?= $emp['nama_lengkap'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="search" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Cari Nama</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan nama..." value="<?= $filters['search'] ?>">
            </div>

            <div class="col-md-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-accent flex-grow-1 py-2"><i class="fa-solid fa-filter me-2"></i>Filter Laporan</button>
                <a href="index.php?page=laporan&action=penggajian&export=pdf&<?= http_build_query($filters) ?>" target="_blank" class="btn btn-outline-accent py-2 px-3"><i class="fa-solid fa-print"></i> PDF</a>
                <a href="index.php?page=laporan&action=penggajian&export=excel&<?= http_build_query($filters) ?>" class="btn btn-success py-2 px-3 text-white" style="background-color: #2e7d32; border: none;"><i class="fa-solid fa-file-excel"></i> Excel</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card card-premium">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-premium datatable w-100" data-searching="false" data-length-change="false">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Divisi / Jabatan</th>
                        <th>Tahun/Bulan</th>
                        <th>Periode</th>
                        <th>Gaji Pokok</th>
                        <th>Lembur</th>
                        <th>Potongan</th>
                        <th>Pot. Lainnya</th>
                        <th>Gaji Bersih</th>
                        <th class="text-center no-print" width="5%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $grand_total = 0;
                    foreach ($data as $row): 
                        $grand_total += $row['gaji_bersih'];
                        $total_potongan = $row['potongan_setengah_hari'] + $row['potongan_alpha'] + $row['total_piutang'];
                    ?>
                    <tr>
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
                        <td><?= $row['periode_tahun'] ?> / <?= format_bulan($row['periode_bulan']) ?></td>
                        <td>Periode <?= $row['periode_ke'] ?></td>
                        <td><?= format_rupiah($row['gaji_pokok']) ?></td>
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
                        <td class="text-center no-print">
                            <a href="index.php?page=penggajian&action=delete&id=<?= $row['id'] ?>&redirect=laporan" class="btn btn-sm btn-danger btn-delete-confirm" title="Hapus Data">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php if (!empty($data)): ?>
                <tfoot>
                    <tr class="table-light font-weight-bold" style="border-top: 2px solid var(--accent-border);">
                        <td colspan="9" class="text-end"><strong>TOTAL GAJI BERSIH DIBAYARKAN:</strong></td>
                        <td><strong class="text-accent-primary"><?= format_rupiah($grand_total) ?></strong></td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
