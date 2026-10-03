<!-- views/potongan_lainnya/index.php -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Potongan Lainnya</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola potongan tambahan yang berlaku untuk semua karyawan per periode gajian (15 hari)</p>
            </div>
            <button class="btn btn-accent d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fa-solid fa-plus"></i> Tambah Potongan
            </button>
        </div>
    </div>
</div>

<!-- Info Banner -->
<div class="alert d-flex align-items-start gap-3 mb-4" style="background: linear-gradient(135deg, rgba(220,38,38,0.12), rgba(220,38,38,0.05)); border: 1px solid var(--accent-border); border-radius: 12px;">
    <i class="fa-solid fa-circle-info text-accent-primary mt-1" style="font-size: 1.2rem;"></i>
    <div>
        <strong class="text-accent-primary">Cara Kerja Potongan Lainnya</strong>
        <p class="mb-0 text-muted" style="font-size: 0.875rem;">Semua potongan berstatus <span class="badge bg-success">Aktif</span> akan otomatis mengurangi gaji bersih setiap karyawan saat penggajian diproses. Jika arisan atau potongan lainnya telah selesai, nonaktifkan atau hapus agar tidak memotong gaji periode berikutnya. Riwayat gaji yang sudah tersimpan tidak akan terpengaruh.</p>
    </div>
</div>

<!-- Table Card -->
<div class="card card-premium">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-accent-primary" style="font-weight: 600;">Daftar Potongan</h5>
        <?php
            $total_aktif = 0;
            foreach ($data as $row) {
                if ($row['status_aktif']) $total_aktif += $row['nominal'];
            }
        ?>
        <?php if ($total_aktif > 0): ?>
        <div class="badge d-flex align-items-center gap-2 px-3 py-2" style="background: rgba(220,38,38,0.12); color: var(--accent-primary); border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
            <i class="fa-solid fa-scissors"></i>
            Total Aktif: <?= format_rupiah($total_aktif) ?> / per karyawan / siklus
        </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($data)): ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-scissors text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                <h5 class="text-muted mt-3">Belum Ada Potongan Lainnya</h5>
                <p class="text-muted mb-3">Klik tombol <strong>Tambah Potongan</strong> untuk mendaftarkan potongan baru.</p>
                <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fa-solid fa-plus me-2"></i> Tambah Potongan Pertama
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-premium datatable w-100">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Potongan</th>
                            <th>Nominal / Karyawan / Siklus</th>
                            <th class="text-center">Status</th>
                            <th>Ditambahkan</th>
                            <th class="text-center" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data as $row): ?>
                        <tr class="<?= !$row['status_aktif'] ? 'opacity-75' : '' ?>">
                            <td><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:<?= $row['status_aktif'] ? 'rgba(220,38,38,0.12)' : 'rgba(108,117,125,0.1)' ?>;flex-shrink:0;">
                                        <i class="fa-solid fa-scissors" style="color:<?= $row['status_aktif'] ? 'var(--accent-primary)' : '#6c757d' ?>;font-size:0.85rem;"></i>
                                    </div>
                                    <strong style="<?= !$row['status_aktif'] ? 'text-decoration:line-through; color:#6c757d;' : '' ?>">
                                        <?= htmlspecialchars($row['nama_potongan']) ?>
                                    </strong>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold <?= $row['status_aktif'] ? 'text-danger' : 'text-muted' ?>" style="font-size: 1rem;">
                                    <?= $row['status_aktif'] ? '-' : '' ?><?= format_rupiah($row['nominal']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($row['status_aktif']): ?>
                                    <span class="badge bg-success px-3 py-2" style="border-radius: 20px; font-size: 0.8rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-3 py-2" style="border-radius: 20px; font-size: 0.8rem;">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.85rem;"><?= date('d M Y', strtotime($row['created_at'])) ?></span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Kelola Karyawan -->
                                    <button type="button" class="btn btn-sm btn-outline-info btn-karyawan"
                                            data-id="<?= $row['id'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_potongan']) ?>"
                                            data-karyawan-ids='<?= json_encode($row['karyawan_ids']) ?>'
                                            data-bs-toggle="modal" data-bs-target="#karyawanModal"
                                            title="Kelola Anggota Karyawan">
                                        <i class="fa-solid fa-users"></i>
                                    </button>
                                    <!-- Toggle Aktif/Nonaktif -->
                                    <?php if ($row['status_aktif']): ?>
                                        <a href="index.php?page=potongan_lainnya&action=toggle&id=<?= $row['id'] ?>&status=0" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Nonaktifkan Potongan"
                                           onclick="return confirm('Nonaktifkan potongan ini? Gaji periode berikutnya tidak akan terpotong lagi.')">
                                            <i class="fa-solid fa-pause"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?page=potongan_lainnya&action=toggle&id=<?= $row['id'] ?>&status=1" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Aktifkan Potongan"
                                           onclick="return confirm('Aktifkan kembali potongan ini?')">
                                            <i class="fa-solid fa-play"></i>
                                        </a>
                                    <?php endif; ?>
                                    <!-- Edit -->
                                    <button type="button" class="btn btn-sm btn-outline-accent btn-edit"
                                            data-id="<?= $row['id'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_potongan']) ?>"
                                            data-nominal="<?= (int)$row['nominal'] ?>"
                                            data-status="<?= $row['status_aktif'] ?>"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                            title="Edit Potongan">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <!-- Hapus -->
                                    <a href="index.php?page=potongan_lainnya&action=delete&id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-danger btn-delete-confirm btn-icon"
                                       title="Hapus Potongan">
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

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title text-accent-primary" id="createModalLabel" style="font-weight: 700;">
                    <i class="fa-solid fa-scissors me-2"></i> Tambah Potongan Lainnya
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=potongan_lainnya&action=create" method="POST">
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="nama_potongan" class="form-label" style="font-weight: 500;">Nama Potongan</label>
                        <input type="text" class="form-control" id="nama_potongan" name="nama_potongan" 
                               placeholder="Contoh: Arisan, Infaq, Dana Sosial..." required>
                        <div class="form-text">Nama deskriptif untuk membedakan jenis potongan.</div>
                    </div>
                    <div class="mb-3">
                        <label for="nominal" class="form-label" style="font-weight: 500;">Nominal per Karyawan per Siklus (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" min="1000" step="1000" class="form-control" id="nominal" name="nominal" 
                                   placeholder="300000" required>
                        </div>
                        <div class="form-text">Jumlah yang dipotong dari gaji setiap karyawan per 15 hari (satu siklus gajian).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Status</label>
                        <select class="form-select shadow-none" name="status_aktif">
                            <option value="1">Aktif (langsung berlaku)</option>
                            <option value="0">Nonaktif (simpan tapi tidak berlaku)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-accent" style="border-radius: 8px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Potongan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title text-accent-primary" id="editModalLabel" style="font-weight: 700;">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Ubah Potongan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=potongan_lainnya&action=edit" method="POST">
                <div class="modal-body px-4">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="edit-nama_potongan" class="form-label" style="font-weight: 500;">Nama Potongan</label>
                        <input type="text" class="form-control" id="edit-nama_potongan" name="nama_potongan" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-nominal" class="form-label" style="font-weight: 500;">Nominal per Karyawan per Siklus (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" min="1000" step="1000" class="form-control" id="edit-nominal" name="nominal" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Status</label>
                        <select class="form-select shadow-none" id="edit-status_aktif" name="status_aktif">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-accent" style="border-radius: 8px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Karyawan Mapping Modal -->
<div class="modal fade" id="karyawanModal" tabindex="-1" aria-labelledby="karyawanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title text-accent-primary" id="karyawanModalLabel" style="font-weight: 700;">
                    <i class="fa-solid fa-users me-2"></i> Kelola Karyawan Terpotong
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=potongan_lainnya&action=save_karyawan" method="POST">
                <div class="modal-body px-4">
                    <input type="hidden" id="karyawan-potongan-id" name="id_potongan">
                    <p class="text-muted">Pilih karyawan yang akan dikenakan potongan <strong id="karyawan-potongan-nama" class="text-accent-primary"></strong>. Karyawan yang tidak dicentang tidak akan terpotong gajinya.</p>
                    
                    <!-- Search and Select All -->
                    <div class="row g-3 mb-3 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control border-start-0 shadow-none" id="search-karyawan" placeholder="Cari nama atau NIK karyawan...">
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input" type="checkbox" id="check-all-karyawan">
                                <label class="form-check-label fw-bold" for="check-all-karyawan" style="cursor: pointer;">
                                    Pilih Semua
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Checklist Box Container -->
                    <div class="card bg-light border-0" style="max-height: 350px; overflow-y: auto; border-radius: 12px;">
                        <div class="card-body p-3">
                            <div class="row" id="karyawan-list-container">
                                <?php foreach ($karyawan as $emp): ?>
                                <div class="col-md-6 mb-2 karyawan-row" data-nama="<?= strtolower(htmlspecialchars($emp['nama_lengkap'])) ?>" data-nik="<?= strtolower(htmlspecialchars($emp['nik'])) ?>">
                                    <div class="p-2 bg-white rounded border d-flex align-items-center gap-3">
                                        <input class="form-check-input check-karyawan-item" type="checkbox" 
                                               name="karyawan_ids[]" 
                                               value="<?= $emp['id'] ?>" 
                                               id="chk-emp-<?= $emp['id'] ?>"
                                               style="width: 18px; height: 18px; cursor: pointer;">
                                        <label class="form-check-label w-100" for="chk-emp-<?= $emp['id'] ?>" style="cursor: pointer; font-size: 0.9rem;">
                                            <strong class="d-block text-dark"><?= htmlspecialchars($emp['nama_lengkap']) ?></strong>
                                            <span class="text-muted d-block" style="font-size: 0.75rem;">NIK: <?= htmlspecialchars($emp['nik']) ?></span>
                                            <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.7rem; font-weight: 500;"><?= htmlspecialchars($emp['nama_divisi']) ?> / <?= htmlspecialchars($emp['nama_jabatan']) ?></span>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-accent" style="border-radius: 8px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pilihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit Modal Populate
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-nama_potongan').value = this.dataset.nama;
            document.getElementById('edit-nominal').value = this.dataset.nominal;
            document.getElementById('edit-status_aktif').value = this.dataset.status;
        });
    });

    // Karyawan Modal Populate
    document.querySelectorAll('.btn-karyawan').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            const karyawanIds = JSON.parse(this.dataset.karyawanIds || '[]');

            document.getElementById('karyawan-potongan-id').value = id;
            document.getElementById('karyawan-potongan-nama').innerText = nama;

            // Reset search input and search display
            const searchInput = document.getElementById('search-karyawan');
            if (searchInput) {
                searchInput.value = '';
            }
            document.querySelectorAll('.karyawan-row').forEach(row => {
                row.style.display = '';
            });

            // Reset all checkboxes
            document.querySelectorAll('.check-karyawan-item').forEach(chk => {
                chk.checked = false;
            });

            // Check checkboxes based on mapping
            karyawanIds.forEach(empId => {
                const chk = document.getElementById('chk-emp-' + empId);
                if (chk) {
                    chk.checked = true;
                }
            });

            // Update check-all checkbox state
            updateCheckAllState();
        });
    });

    // Search filter in modal
    const searchInput = document.getElementById('search-karyawan');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.karyawan-row').forEach(row => {
                const nama = row.dataset.nama;
                const nik = row.dataset.nik;
                if (nama.includes(query) || nik.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            updateCheckAllState();
        });
    }

    // Toggle Check All
    const checkAll = document.getElementById('check-all-karyawan');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.check-karyawan-item').forEach(chk => {
                const parentRow = chk.closest('.karyawan-row');
                if (parentRow && parentRow.style.display !== 'none') {
                    chk.checked = checkAll.checked;
                }
            });
        });
    }

    function updateCheckAllState() {
        const checkAll = document.getElementById('check-all-karyawan');
        if (!checkAll) return;
        const visibleCheckboxes = Array.from(document.querySelectorAll('.check-karyawan-item')).filter(chk => {
            const parentRow = chk.closest('.karyawan-row');
            return parentRow && parentRow.style.display !== 'none';
        });
        
        if (visibleCheckboxes.length === 0) {
            checkAll.checked = false;
            return;
        }

        const allChecked = visibleCheckboxes.every(chk => chk.checked);
        checkAll.checked = allChecked;
    }

    // Update check all if individual checkbox is clicked
    document.querySelectorAll('.check-karyawan-item').forEach(chk => {
        chk.addEventListener('change', updateCheckAllState);
    });
});
</script>
