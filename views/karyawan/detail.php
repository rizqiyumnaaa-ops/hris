<!-- Karyawan Detail View -->
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="index.php?page=karyawan" class="btn btn-outline-accent py-2"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Detail Profil Karyawan</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Informasi lengkap data diri, jabatan, dan parameter gaji</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Avatar & Quick Info -->
    <div class="col-lg-4 col-md-5 mb-4">
        <div class="card card-premium text-center h-100">
            <div class="card-body py-5 d-flex flex-column align-items-center justify-content-center">
                <?php 
                $foto_path = !empty($karyawan['foto']) && file_exists('assets/uploads/foto_karyawan/' . $karyawan['foto']) 
                    ? 'assets/uploads/foto_karyawan/' . $karyawan['foto'] 
                    : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
                ?>
                <img src="<?= $foto_path ?>" alt="Foto Profil" class="rounded-circle object-fit-cover mb-4" style="width: 150px; height: 150px; border: 4px solid var(--accent-soft);">
                
                <h4 class="mb-1" style="font-weight: 700;"><?= $karyawan['nama_lengkap'] ?></h4>
                <p class="text-muted mb-3" style="font-size: 0.9rem;">NIK: <strong><?= $karyawan['nik'] ?></strong></p>
                
                <div class="d-flex gap-2 mb-4 justify-content-center">
                    <span class="badge bg-light text-dark border"><?= $karyawan['nama_divisi'] ?></span>
                    <span class="badge bg-light text-dark border"><?= $karyawan['nama_jabatan'] ?></span>
                </div>
                
                <div class="w-100 border-top pt-4">
                    <div class="row g-2 text-start px-3">
                        <div class="col-6 text-muted" style="font-size: 0.85rem;">Status Kepegawaian</div>
                        <div class="col-6 text-end" style="font-weight: 600; font-size: 0.85rem;"><?= $karyawan['status_kerja'] ?></div>
                        
                        <div class="col-6 text-muted" style="font-size: 0.85rem;">Status Akun</div>
                        <div class="col-6 text-end" style="font-size: 0.85rem;">
                            <?php if ($karyawan['status_aktif'] == 1): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 w-100 px-3 mt-4">
                    <a href="index.php?page=karyawan&action=edit&id=<?= $karyawan['id'] ?>" class="btn btn-accent"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Data</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Detail Information Tabs -->
    <div class="col-lg-8 col-md-7 mb-4">
        <div class="card card-premium h-100">
            <div class="card-header">
                <h5 class="mb-0 text-accent-primary" style="font-size: 1.1rem; font-weight: 700;">Informasi Detail Kepegawaian</h5>
            </div>
            <div class="card-body">
                <h6 class="border-bottom pb-2 mb-3 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px; color: var(--accent-primary); font-weight: 700;">Data Pribadi & Kontak</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <span class="text-muted d-block" style="font-size: 0.85rem;">Nomor HP / WhatsApp</span>
                        <strong style="font-size: 0.95rem;"><?= $karyawan['no_hp'] ?></strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted d-block" style="font-size: 0.85rem;">Tanggal Bergabung (Masuk)</span>
                        <strong style="font-size: 0.95rem;"><?= format_tanggal($karyawan['tanggal_masuk']) ?></strong>
                    </div>
                    <div class="col-12">
                        <span class="text-muted d-block" style="font-size: 0.85rem;">Alamat Rumah</span>
                        <strong style="font-size: 0.95rem;"><?= nl2br($karyawan['alamat']) ?></strong>
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px; color: var(--accent-primary); font-weight: 700;">Parameter Finansial / Gaji</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border" style="border-color: var(--accent-border) !important;">
                            <span class="text-muted d-block" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Gaji Per Hari</span>
                            <h3 class="mb-0 mt-1" style="color: var(--accent-primary); font-weight: 700; font-size: 1.5rem;"><?= format_rupiah($karyawan['gaji_pokok']) ?></h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border" style="border-color: var(--accent-border) !important;">
                            <span class="text-muted d-block" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Tarif Lembur per Jam</span>
                            <h3 class="mb-0 mt-1" style="color: var(--accent-primary); font-weight: 700; font-size: 1.5rem;"><?= format_rupiah($karyawan['tarif_lembur']) ?></h3>
                        </div>
                    </div>
                </div>
                
                <h6 class="border-bottom pb-2 mb-3 mt-4 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px; color: var(--accent-primary); font-weight: 700;">Catatan Tambahan</h6>
                <div class="text-muted" style="font-size: 0.9rem; line-height: 1.6;">
                    Gaji dihitung berdasarkan <strong>Gaji Per Hari × jumlah hari hadir</strong> dalam periode (Periode I: tgl 1-15, Periode II: tgl 16-akhir bulan). Setengah hari dihitung 0.5×, alpha dipotong penuh 1×. Pastikan nilai gaji harian sudah benar.
                </div>
            </div>
        </div>
    </div>
</div>
