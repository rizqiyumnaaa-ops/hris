<!-- Lembur Management View -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">
                    <?= is_admin() ? 'Data Lembur Karyawan' : 'Data Lembur Saya' ?>
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    <?= is_admin()
                        ? 'Kelola pencatatan jam lembur dan nominal kompensasi lembur karyawan'
                        : 'Catatan jam lembur Anda. Anda dapat menambah, mengubah, dan menghapus catatan lembur milik Anda.' ?>
                </p>
            </div>
            <button type="button" class="btn btn-accent d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus"></i> Catat Lembur
            </button>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-premium mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="page" value="lembur">
            
            <div class="col-md-4">
                <label for="start_date" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $filters['start_date'] ?>">
            </div>

            <div class="col-md-4">
                <label for="end_date" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $filters['end_date'] ?>">
            </div>

            <?php if (is_admin()): ?>
            <div class="col-md-2">
                <label for="id_karyawan" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Pilih Karyawan</label>
                <select class="form-select shadow-none" id="id_karyawan" name="id_karyawan">
                    <option value="">-- Semua Karyawan --</option>
                    <?php foreach ($karyawan as $emp): ?>
                    <option value="<?= $emp['id'] ?>" <?= $filters['id_karyawan'] == $emp['id'] ? 'selected' : '' ?>><?= $emp['nik'] ?> - <?= $emp['nama_lengkap'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1">
                <label for="search" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Cari Nama</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Nama..." value="<?= $filters['search'] ?>">
            </div>
            <?php endif; ?>

            <div class="col-md-<?= is_admin() ? '1' : '4' ?> d-flex align-items-end">
                <button type="submit" class="btn btn-outline-accent w-100 py-2"><i class="fa-solid fa-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card card-premium">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-premium table-responsive-stack datatable w-100" data-searching="false" data-length-change="false">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <?php if (is_admin()): ?>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <?php endif; ?>
                        <th>Tanggal</th>
                        <th>Jam Lembur</th>
                        <th>Tarif per Jam</th>
                        <th>Total Lembur</th>
                        <th>Keterangan</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $karyawan_id_saya = get_session_karyawan_id();
                    foreach ($data as $row): 
                    ?>
                    <tr>
                        <td data-label="No"><?= $no++ ?></td>
                        <?php if (is_admin()): ?>
                        <td data-label="NIK"><strong><?= $row['nik'] ?></strong></td>
                        <td data-label="Nama Karyawan"><?= $row['nama_lengkap'] ?></td>
                        <?php endif; ?>
                        <td data-label="Tanggal"><?= format_tanggal($row['tanggal']) ?></td>
                        <td data-label="Jam Lembur"><strong><?= number_format($row['jumlah_jam'], 1, ',', '.') ?> Jam</strong></td>
                        <td data-label="Tarif per Jam"><?= format_rupiah($row['tarif_lembur']) ?></td>
                        <td data-label="Total Lembur"><strong class="text-success"><?= format_rupiah($row['total_lembur']) ?></strong></td>
                        <td data-label="Keterangan"><span class="text-muted" style="font-size: 0.85rem;"><?= !empty($row['keterangan']) ? $row['keterangan'] : '-' ?></span></td>
                        <td data-label="Aksi" class="text-center">
                            <?php 
                            // Admin bisa edit/hapus semua. Pegawai hanya bisa edit/hapus milik sendiri
                            $can_modify = is_admin() || (int)$row['id_karyawan'] === (int)$karyawan_id_saya;
                            ?>
                            <?php if ($can_modify): ?>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-accent btn-edit" 
                                        data-id="<?= $row['id'] ?>" 
                                        data-karyawan="<?= $row['id_karyawan'] ?>"
                                        data-tanggal="<?= $row['tanggal'] ?>"
                                        data-jam="<?= $row['jumlah_jam'] ?>"
                                        data-keterangan="<?= $row['keterangan'] ?>"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="index.php?page=lembur&action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete-confirm btn-icon">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title text-accent-primary" id="addModalLabel" style="font-weight: 700;">Catat Lembur Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=lembur&action=create" method="POST">
                <div class="modal-body px-4">
                    <?php if (is_admin()): ?>
                    <div class="mb-3">
                        <label for="add-id_karyawan" class="form-label" style="font-weight: 500;">Pilih Karyawan</label>
                        <select class="form-select shadow-none" id="add-id_karyawan" name="id_karyawan" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= $emp['nik'] ?> - <?= $emp['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <!-- Pegawai: karyawan_id otomatis dari session, tampilkan nama saja -->
                    <?php if (!empty($karyawan[0])): ?>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Karyawan</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($karyawan[0]['nama_lengkap'] ?? '') ?>" disabled>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="add-tanggal" class="form-label" style="font-weight: 500;">Tanggal Lembur</label>
                        <input type="date" class="form-control" id="add-tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-jumlah_jam" class="form-label" style="font-weight: 500;">Jumlah Jam Lembur</label>
                        <input type="number" step="0.1" min="0.5" class="form-control" id="add-jumlah_jam" name="jumlah_jam" placeholder="Contoh: 2.5" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-keterangan" class="form-label" style="font-weight: 500;">Keterangan</label>
                        <textarea class="form-control" id="add-keterangan" name="keterangan" rows="2" placeholder="Tugas lembur... (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-accent" style="border-radius: 8px;">Simpan</button>
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
                <h5 class="modal-title text-accent-primary" id="editModalLabel" style="font-weight: 700;">Ubah Catatan Lembur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=lembur&action=edit" method="POST">
                <div class="modal-body px-4">
                    <input type="hidden" id="edit-id" name="id">
                    <?php if (is_admin()): ?>
                    <div class="mb-3">
                        <label for="edit-id_karyawan" class="form-label" style="font-weight: 500;">Karyawan</label>
                        <select class="form-select shadow-none" id="edit-id_karyawan" name="id_karyawan" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= $emp['nik'] ?> - <?= $emp['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="id_karyawan" value="<?= get_session_karyawan_id() ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="edit-tanggal" class="form-label" style="font-weight: 500;">Tanggal Lembur</label>
                        <input type="date" class="form-control" id="edit-tanggal" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-jumlah_jam" class="form-label" style="font-weight: 500;">Jumlah Jam Lembur</label>
                        <input type="number" step="0.1" min="0.5" class="form-control" id="edit-jumlah_jam" name="jumlah_jam" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-keterangan" class="form-label" style="font-weight: 500;">Keterangan</label>
                        <textarea class="form-control" id="edit-keterangan" name="keterangan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-accent" style="border-radius: 8px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id         = this.getAttribute('data-id');
                const karyawan   = this.getAttribute('data-karyawan');
                const tanggal    = this.getAttribute('data-tanggal');
                const jam        = this.getAttribute('data-jam');
                const keterangan = this.getAttribute('data-keterangan');

                document.getElementById('edit-id').value          = id;
                document.getElementById('edit-tanggal').value     = tanggal;
                document.getElementById('edit-jumlah_jam').value  = jam;
                document.getElementById('edit-keterangan').value  = keterangan;

                // Admin: set dropdown karyawan
                const selKaryawan = document.getElementById('edit-id_karyawan');
                if (selKaryawan) selKaryawan.value = karyawan;
            });
        });
    });
</script>
