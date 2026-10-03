<!-- Karyawan Edit View -->
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="index.php?page=karyawan" class="btn btn-outline-accent py-2"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Ubah Data Karyawan</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Perbarui data profil, jabatan, dan payroll karyawan</p>
            </div>
        </div>
    </div>
</div>

<form action="index.php?page=karyawan&action=edit&id=<?= $karyawan['id'] ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="row">
        <!-- Profile & Upload Photo -->
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card card-premium text-center">
                <div class="card-body py-5">
                    <?php 
                    $foto_path = !empty($karyawan['foto']) && file_exists('assets/uploads/foto_karyawan/' . $karyawan['foto']) 
                        ? 'assets/uploads/foto_karyawan/' . $karyawan['foto'] 
                        : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
                    ?>
                    <img id="profile-preview" src="<?= $foto_path ?>" alt="Preview Foto" class="rounded-circle object-fit-cover mb-4" style="width: 140px; height: 140px; border: 4px solid var(--accent-soft);">
                    <h5 class="mb-2" style="font-weight: 600;"><?= $karyawan['nama_lengkap'] ?></h5>
                    <p class="text-muted mb-2" style="font-size: 0.85rem;">NIK: <strong><?= $karyawan['nik'] ?></strong></p>
                    <p class="text-muted mb-4" style="font-size: 0.85rem;">Ukuran file maksimal 2MB dengan ekstensi JPG, JPEG, atau PNG</p>
                    
                    <div class="input-group justify-content-center">
                        <label class="btn btn-outline-accent px-4 py-2" style="border-radius: 8px; cursor: pointer;">
                            <i class="fa-solid fa-cloud-arrow-up me-2"></i> Pilih Foto Baru
                            <input type="file" name="foto" id="foto-input" accept="image/*" class="d-none" onchange="previewImage(event)">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal & Job details -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="card card-premium">
                <div class="card-header">
                    <h5 class="mb-0" style="color: var(--accent-primary); font-size: 1.05rem;">Formulir Data Diri & Jabatan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight: 500;">NIK (Nomor Induk Karyawan)</label>
                            <input type="text" class="form-control bg-light" value="<?= $karyawan['nik'] ?>" readonly disabled>
                            <small class="text-muted">NIK tidak dapat diubah setelah didaftarkan.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="nama_lengkap" class="form-label" style="font-weight: 500;">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" value="<?= $karyawan['nama_lengkap'] ?>" required>
                            <div class="invalid-feedback">Nama lengkap wajib diisi.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="id_divisi" class="form-label" style="font-weight: 500;">Divisi Kerja</label>
                            <select class="form-select" id="id_divisi" name="id_divisi" required>
                                <option value="">-- Pilih Divisi --</option>
                                <?php foreach ($divisi as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $karyawan['id_divisi'] == $d['id'] ? 'selected' : '' ?>><?= $d['nama_divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Silakan pilih divisi kerja.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="id_jabatan" class="form-label" style="font-weight: 500;">Jabatan</label>
                            <select class="form-select" id="id_jabatan" name="id_jabatan" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <?php foreach ($jabatan as $j): ?>
                                <option value="<?= $j['id'] ?>" <?= $karyawan['id_jabatan'] == $j['id'] ? 'selected' : '' ?>><?= $j['nama_jabatan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Silakan pilih jabatan.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="no_hp" class="form-label" style="font-weight: 500;">Nomor HP / WhatsApp</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: 0812XXXXXXXX" value="<?= $karyawan['no_hp'] ?>" required pattern="^[0-9+ ]{10,15}$">
                            <div class="invalid-feedback">Masukkan nomor HP yang valid (10-15 digit angka).</div>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal_masuk" class="form-label" style="font-weight: 500;">Tanggal Masuk Kerja</label>
                            <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?= $karyawan['tanggal_masuk'] ?>" required>
                            <div class="invalid-feedback">Pilih tanggal masuk kerja.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="status_kerja" class="form-label" style="font-weight: 500;">Status Hubungan Kerja</label>
                            <select class="form-select" id="status_kerja" name="status_kerja" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Tetap" <?= $karyawan['status_kerja'] == 'Tetap' ? 'selected' : '' ?>>Karyawan Tetap</option>
                                <option value="Kontrak" <?= $karyawan['status_kerja'] == 'Kontrak' ? 'selected' : '' ?>>Karyawan Kontrak</option>
                                <option value="Magang" <?= $karyawan['status_kerja'] == 'Magang' ? 'selected' : '' ?>>Magang</option>
                            </select>
                            <div class="invalid-feedback">Pilih status kerja.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="gaji_pokok" class="form-label" style="font-weight: 500;">Gaji Per Hari (Rp)</label>
                            <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" placeholder="Contoh: 200000" min="0" value="<?= (int)$karyawan['gaji_pokok'] ?>" required>
                            <div class="form-text">Gaji harian × hari hadir = gaji periode.</div>
                            <div class="invalid-feedback">Gaji per hari wajib diisi.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="tarif_lembur" class="form-label" style="font-weight: 500;">Tarif Lembur per Jam (Rp)</label>
                            <input type="number" class="form-control" id="tarif_lembur" name="tarif_lembur" placeholder="Contoh: 35000" min="0" value="<?= (int)$karyawan['tarif_lembur'] ?>" required>
                            <div class="invalid-feedback">Tarif lembur wajib diisi.</div>
                        </div>

                        <div class="col-md-6 d-flex align-items-center mt-md-5">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif" value="1" <?= $karyawan['status_aktif'] == 1 ? 'checked' : '' ?> style="width: 45px; height: 22px;">
                                <label class="form-check-label ms-2" for="status_aktif" style="font-weight: 500;">Status Karyawan Aktif</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="alamat" class="form-label" style="font-weight: 500;">Alamat Tempat Tinggal</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap rumah" required><?= $karyawan['alamat'] ?></textarea>
                            <div class="invalid-feedback">Alamat lengkap wajib diisi.</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top-0 d-flex justify-content-end gap-2 pb-4 px-4 bg-transparent">
                    <button type="button" onclick="history.back()" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-accent px-4 py-2" style="border-radius: 8px;">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('profile-preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    } else {
                        $('#loader-overlay').fadeIn(100);
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
