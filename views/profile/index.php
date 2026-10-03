<?php
/**
 * View: Profil Admin - index.php
 */

$page_title = 'Profil Saya';
require_once __DIR__ . '/../templates/header.php';

$foto_src = !empty($user['foto']) ? 'assets/uploads/admins/' . basename($user['foto']) : null;
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold" style="color: var(--accent-primary);">
            <i class="fa-solid fa-circle-user me-2"></i>Profil Saya
        </h4>
        <p class="text-muted mb-0" style="font-size:0.9rem;">Kelola informasi akun, foto profil, dan keamanan password.</p>
    </div>
</div>

<?php $flash = get_flash_message(); if ($flash): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({ icon: '<?= $flash['type'] ?>', title: '<?= $flash['title'] ?>', text: '<?= $flash['text'] ?>', confirmButtonColor: '#dc2626', timer: 3000, timerProgressBar: true });
});
</script>
<?php endif; ?>

<div class="row g-4">

    <!-- Kolom kiri: Foto Profil -->
    <div class="col-lg-4">
        <div class="card-premium h-100">
            <div class="card-header fw-semibold"><i class="fa-solid fa-image me-2 text-muted"></i>Foto Profil</div>
            <div class="card-body text-center">
                <!-- Preview foto -->
                <div class="mb-3">
                    <?php if ($foto_src): ?>
                        <img id="foto-preview" src="<?= htmlspecialchars($foto_src) ?>" alt="Foto Profil"
                             class="rounded-circle border"
                             style="width:130px;height:130px;object-fit:cover;border:3px solid var(--accent-border)!important;">
                    <?php else: ?>
                        <div id="foto-preview-default" class="rounded-circle d-flex align-items-center justify-content-center mx-auto"
                             style="width:130px;height:130px;background:var(--accent-soft);font-size:3rem;color:var(--accent-primary);">
                            <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                        </div>
                        <img id="foto-preview" src="" alt="Foto Profil"
                             class="rounded-circle border d-none mx-auto"
                             style="width:130px;height:130px;object-fit:cover;border:3px solid var(--accent-border)!important;">
                    <?php endif; ?>
                </div>

                <h5 class="fw-bold mb-0"><?= htmlspecialchars($user['nama_lengkap']) ?></h5>
                <p class="text-muted" style="font-size:0.85rem;">@<?= htmlspecialchars($user['username']) ?></p>

                <!-- Form ganti foto -->
                <form action="index.php?page=profile&action=update_photo" method="POST"
                      enctype="multipart/form-data" id="form-foto">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">PILIH FOTO BARU</label>
                        <input type="file" class="form-control" name="foto" id="input-foto"
                               accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="form-text">JPG / PNG / WebP. Maks 2 MB.</div>
                    </div>
                    <button type="submit" class="btn btn-accent w-100">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i>Simpan Foto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom kanan: Form Edit Info & Password -->
    <div class="col-lg-8">

        <!-- Form Edit Informasi -->
        <div class="card-premium mb-4">
            <div class="card-header fw-semibold"><i class="fa-solid fa-pen-to-square me-2 text-muted"></i>Informasi Akun</div>
            <div class="card-body">
                <form action="index.php?page=profile&action=update" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">NAMA LENGKAP</label>
                            <input type="text" class="form-control" name="nama_lengkap"
                                   value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">USERNAME</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent" style="border-color:var(--accent-border);">@</span>
                                <input type="text" class="form-control" name="username"
                                       value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-accent px-4">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form Ganti Password -->
        <div class="card-premium">
            <div class="card-header fw-semibold"><i class="fa-solid fa-lock me-2 text-muted"></i>Ganti Password</div>
            <div class="card-body">
                <form action="index.php?page=profile&action=update_password" method="POST">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">PASSWORD LAMA</label>
                            <div class="input-group">
                                <input type="password" class="form-control border-end-0" name="password_lama"
                                       id="old_pass" placeholder="Masukkan password lama" required>
                                <button class="btn btn-outline-secondary border toggle-pass" type="button"
                                        data-target="old_pass" style="border-color:var(--accent-border);">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">PASSWORD BARU</label>
                            <div class="input-group">
                                <input type="password" class="form-control border-end-0" name="password_baru"
                                       id="new_pass" placeholder="Min. 6 karakter" required>
                                <button class="btn btn-outline-secondary border toggle-pass" type="button"
                                        data-target="new_pass" style="border-color:var(--accent-border);">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">KONFIRMASI PASSWORD</label>
                            <div class="input-group">
                                <input type="password" class="form-control border-end-0" name="password_konfirmasi"
                                       id="confirm_pass" placeholder="Ulangi password baru" required>
                                <button class="btn btn-outline-secondary border toggle-pass" type="button"
                                        data-target="confirm_pass" style="border-color:var(--accent-border);">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-warning py-2 px-3 mb-0" style="font-size:0.85rem;border-radius:10px;">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Setelah ganti password, kamu akan otomatis <strong>logout</strong> dan harus login ulang.
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-danger px-4" id="btn-ganti-pw">
                                <i class="fa-solid fa-key me-2"></i>Ganti Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
// Preview foto sebelum upload
document.getElementById('input-foto').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('foto-preview');
        const defEl   = document.getElementById('foto-preview-default');
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        if (defEl) defEl.classList.add('d-none');
    };
    reader.readAsDataURL(file);
});

// Toggle show/hide password
document.querySelectorAll('.toggle-pass').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const target = document.getElementById(this.dataset.target);
        const icon   = this.querySelector('i');
        if (target.type === 'password') {
            target.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            target.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
});

// Konfirmasi ganti password
document.getElementById('btn-ganti-pw').closest('form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
        icon: 'warning',
        title: 'Ganti Password?',
        text: 'Kamu akan otomatis logout setelah password berhasil diubah.',
        showCancelButton: true,
        confirmButtonColor: '#c62828',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Ya, Ganti!',
        cancelButtonText: 'Batal'
    }).then(result => { if (result.isConfirmed) form.submit(); });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
