<?php
/**
 * View: Manajemen Akun (Admin & Pegawai)
 */

$page_title = 'Kelola Akun';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-1 fw-bold" style="color: var(--accent-primary);">
            <i class="fa-solid fa-users-gear me-2"></i>Kelola Akun
        </h4>
        <p class="text-muted mb-0" style="font-size:0.9rem;">Kelola akun administrator dan akun pegawai untuk login ke sistem HRIS.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-accent" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
            <i class="fa-solid fa-user-shield me-2"></i>Tambah Admin
        </button>
        <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#modalTambahPegawai">
            <i class="fa-solid fa-user-plus me-2"></i>Buat Akun Pegawai
        </button>
    </div>
</div>

<?php $flash = get_flash_message(); if ($flash): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({ icon: '<?= $flash['type'] ?>', title: '<?= $flash['title'] ?>', text: '<?= $flash['text'] ?>', confirmButtonColor: '#dc2626', timer: 3000, timerProgressBar: true });
});
</script>
<?php endif; ?>

<!-- Tabel Daftar Admin -->
<div class="card-premium mb-4">
    <div class="card-header d-flex align-items-center gap-2 fw-semibold">
        <i class="fa-solid fa-user-shield text-muted"></i>
        Akun Administrator
        <span class="badge ms-auto" style="background:var(--accent-soft);color:var(--accent-primary);">
            <?= count(array_filter($users, fn($u) => $u['role'] === 'admin_utama' || $u['role'] === 'admin_divisi')) ?> Admin
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-premium mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role Akses</th>
                        <th>Divisi</th>
                        <th>Foto</th>
                        <th>Dibuat</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $admins = array_filter($users, fn($u) => $u['role'] === 'admin_utama' || $u['role'] === 'admin_divisi');
                    if (empty($admins)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada admin.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $i = 1; foreach ($admins as $adm): ?>
                    <tr>
                        <td class="fw-semibold text-muted"><?= $i++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (!empty($adm['foto'])): ?>
                                    <img src="<?= htmlspecialchars($adm['foto']) ?>" alt="foto"
                                         class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                                <?php else: ?>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                         style="width:38px;height:38px;background:var(--accent-soft);color:var(--accent-primary);font-size:1rem;">
                                        <?= strtoupper(substr($adm['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="fw-semibold"><?= htmlspecialchars($adm['nama_lengkap']) ?></span>
                                <?php if ((int)$adm['id'] === (int)$_SESSION['user_id']): ?>
                                    <span class="badge text-bg-warning" style="font-size:0.7rem;">Anda</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><code>@<?= htmlspecialchars($adm['username']) ?></code></td>
                        <td>
                            <?php if ($adm['role'] === 'admin_utama'): ?>
                                <span class="badge bg-primary"><i class="fa-solid fa-shield-halved me-1"></i>Admin Utama</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-user-shield me-1"></i>Admin Divisi</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($adm['role'] === 'admin_divisi'): ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($adm['nama_divisi'] ?? '-') ?></span>
                            <?php else: ?>
                                <span class="text-muted">Semua Divisi</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($adm['foto'])): ?>
                                <span class="badge text-bg-success"><i class="fa-solid fa-check me-1"></i>Ada</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-size:0.85rem;">
                            <?= date('d M Y', strtotime($adm['created_at'])) ?>
                        </td>
                        <td class="text-center">
                            <?php if ((int)$adm['id'] !== (int)$_SESSION['user_id']): ?>
                            <a href="#" class="btn btn-sm btn-danger btn-hapus-akun"
                               data-id="<?= $adm['id'] ?>"
                               data-nama="<?= htmlspecialchars($adm['nama_lengkap']) ?>">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                            <?php else: ?>
                            <span class="text-muted" style="font-size:0.8rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabel Daftar Pegawai -->
<div class="card-premium">
    <div class="card-header d-flex align-items-center gap-2 fw-semibold">
        <i class="fa-solid fa-id-card text-muted"></i>
        Akun Pegawai
        <span class="badge ms-auto" style="background:var(--accent-soft);color:var(--accent-primary);">
            <?= count(array_filter($users, fn($u) => $u['role'] === 'pegawai')) ?> Pegawai
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-premium mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Pegawai (Karyawan)</th>
                        <th>NIK</th>
                        <th>Username Login</th>
                        <th>Dibuat</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $pegawais = array_filter($users, fn($u) => $u['role'] === 'pegawai');
                    if (empty($pegawais)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-user-slash fa-2x mb-2 d-block"></i>
                            Belum ada akun pegawai. Klik <strong>"Buat Akun Pegawai"</strong> untuk menambahkan.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $i = 1; foreach ($pegawais as $peg): ?>
                    <tr>
                        <td class="fw-semibold text-muted"><?= $i++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                     style="width:34px;height:34px;background:#e8f5e9;color:#388e3c;font-size:0.9rem;flex-shrink:0;">
                                    <?= strtoupper(substr($peg['nama_lengkap'], 0, 1)) ?>
                                </div>
                                <span class="fw-semibold"><?= htmlspecialchars($peg['nama_lengkap']) ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if ($peg['nik']): ?>
                                <code><?= htmlspecialchars($peg['nik']) ?></code>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><code>@<?= htmlspecialchars($peg['username']) ?></code></td>
                        <td class="text-muted" style="font-size:0.85rem;">
                            <?= date('d M Y', strtotime($peg['created_at'])) ?>
                        </td>
                        <td class="text-center">
                            <a href="#" class="btn btn-sm btn-danger btn-hapus-akun"
                               data-id="<?= $peg['id'] ?>"
                               data-nama="<?= htmlspecialchars($peg['nama_lengkap']) ?>">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-labelledby="modalTambahAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--accent-border);">
            <div class="modal-header" style="border-bottom:1px solid var(--accent-border);">
                <h5 class="modal-title fw-bold" id="modalTambahAdminLabel" style="color:var(--accent-primary);">
                    <i class="fa-solid fa-user-shield me-2"></i>Tambah Admin Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?page=admin&action=create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">NAMA LENGKAP</label>
                        <input type="text" class="form-control" name="nama_lengkap"
                               placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">USERNAME</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent" style="border-color:var(--accent-border);">@</span>
                            <input type="text" class="form-control" name="username"
                                   placeholder="Contoh: budi123" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">ROLE AKSES</label>
                        <select class="form-select shadow-none" name="role" id="role-select" required>
                            <option value="admin_divisi">Admin Divisi (Akses Terbatas Divisi)</option>
                            <option value="admin_utama">Admin Utama (Akses Penuh)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="divisi-select-container">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">PILIH DIVISI YANG DIKELOLA</label>
                        <select class="form-select shadow-none" name="id_divisi" id="id-divisi-select" required>
                            <option value="">-- Pilih Divisi --</option>
                            <?php foreach ($divisi as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nama_divisi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">PASSWORD</label>
                        <div class="input-group">
                            <input type="password" class="form-control border-end-0" name="password"
                                   id="adm-pw-baru" placeholder="Min. 6 karakter" required>
                            <button class="btn btn-outline-secondary border toggle-pass" type="button"
                                    data-target="adm-pw-baru" style="border-color:var(--accent-border);">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">KONFIRMASI PASSWORD</label>
                        <div class="input-group">
                            <input type="password" class="form-control border-end-0" name="password_konfirmasi"
                                   id="adm-pw-konfirmasi" placeholder="Ulangi password" required>
                            <button class="btn btn-outline-secondary border toggle-pass" type="button"
                                    data-target="adm-pw-konfirmasi" style="border-color:var(--accent-border);">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--accent-border);">
                    <button type="button" class="btn btn-outline-accent" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-accent">
                        <i class="fa-solid fa-user-shield me-2"></i>Tambah Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Buat Akun Pegawai -->
<div class="modal fade" id="modalTambahPegawai" tabindex="-1" aria-labelledby="modalTambahPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--accent-border);">
            <div class="modal-header" style="border-bottom:1px solid var(--accent-border);">
                <h5 class="modal-title fw-bold" id="modalTambahPegawaiLabel" style="color:var(--accent-primary);">
                    <i class="fa-solid fa-user-plus me-2"></i>Buat Akun Login Pegawai
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?page=admin&action=create_pegawai" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.85rem;">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Akun ini hanya bisa mengakses <strong>Profil</strong> dan <strong>Data Lembur</strong> miliknya sendiri.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">PILIH KARYAWAN</label>
                        <select class="form-select shadow-none" name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nik']) ?> - <?= htmlspecialchars($k['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">USERNAME LOGIN</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent" style="border-color:var(--accent-border);">@</span>
                            <input type="text" class="form-control" name="username"
                                   placeholder="Contoh: budi.santoso" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">PASSWORD</label>
                        <div class="input-group">
                            <input type="password" class="form-control border-end-0" name="password"
                                   id="peg-pw-baru" placeholder="Min. 6 karakter" required>
                            <button class="btn btn-outline-secondary border toggle-pass" type="button"
                                    data-target="peg-pw-baru" style="border-color:var(--accent-border);">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-muted);">KONFIRMASI PASSWORD</label>
                        <div class="input-group">
                            <input type="password" class="form-control border-end-0" name="password_konfirmasi"
                                   id="peg-pw-konfirmasi" placeholder="Ulangi password" required>
                            <button class="btn btn-outline-secondary border toggle-pass" type="button"
                                    data-target="peg-pw-konfirmasi" style="border-color:var(--accent-border);">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--accent-border);">
                    <button type="button" class="btn btn-outline-accent" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-accent">
                        <i class="fa-solid fa-user-plus me-2"></i>Buat Akun Pegawai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Menampilkan/menyembunyikan pilihan divisi berdasarkan role admin baru
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role-select');
    const divisiContainer = document.getElementById('divisi-select-container');
    const idDivisiSelect = document.getElementById('id-divisi-select');

    if (roleSelect) {
        function toggleDivisiSelect() {
            if (roleSelect.value === 'admin_utama') {
                divisiContainer.style.display = 'none';
                idDivisiSelect.removeAttribute('required');
            } else {
                divisiContainer.style.display = 'block';
                idDivisiSelect.setAttribute('required', 'required');
            }
        }
        
        roleSelect.addEventListener('change', toggleDivisiSelect);
        toggleDivisiSelect(); // Jalankan sekali saat load pertama
    }
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

// Konfirmasi hapus akun
document.querySelectorAll('.btn-hapus-akun').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const id   = this.dataset.id;
        const nama = this.dataset.nama;
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Akun?',
            html: `Akun <strong>${nama}</strong> akan dihapus permanen.`,
            showCancelButton: true,
            confirmButtonColor: '#c62828',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = `index.php?page=admin&action=delete&id=${id}`;
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
