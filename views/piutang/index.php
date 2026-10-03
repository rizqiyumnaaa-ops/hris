<!-- Piutang Management View -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Data Piutang Karyawan</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola pinjaman / piutang karyawan yang akan memotong gaji secara otomatis saat payroll</p>
            </div>
            <button type="button" class="btn btn-accent d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus"></i> Catat Piutang
            </button>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-premium mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="page" value="piutang">
            
            <div class="col-md-3">
                <label for="start_date" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $filters['start_date'] ?>">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $filters['end_date'] ?>">
            </div>

            <div class="col-md-2">
                <label for="id_karyawan" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Pilih Karyawan</label>
                <select class="form-select shadow-none" id="id_karyawan" name="id_karyawan">
                    <option value="">-- Semua Karyawan --</option>
                    <?php foreach ($karyawan as $emp): ?>
                    <option value="<?= $emp['id'] ?>" <?= $filters['id_karyawan'] == $emp['id'] ? 'selected' : '' ?>><?= $emp['nik'] ?> - <?= $emp['nama_lengkap'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="status_piutang" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Status</label>
                <select class="form-select shadow-none" id="status_piutang" name="status_piutang">
                    <option value="">-- Semua --</option>
                    <option value="Belum Lunas" <?= $filters['status_piutang'] === 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                    <option value="Lunas" <?= $filters['status_piutang'] === 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-accent w-100 py-2"><i class="fa-solid fa-filter me-2"></i>Filter</button>
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
                        <th>Tanggal Pinjam</th>
                        <th>Nominal Piutang</th>
                        <th>Keterangan</th>
                        <th class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($data as $row): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= $row['nik'] ?></strong></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= format_tanggal($row['tanggal']) ?></td>
                        <td><strong class="text-danger"><?= format_rupiah($row['nominal']) ?></strong></td>
                        <td><span class="text-muted" style="font-size: 0.85rem;"><?= !empty($row['keterangan']) ? $row['keterangan'] : '-' ?></span></td>
                        <td class="text-center">
                            <?php if ($row['status_piutang'] === 'Lunas'): ?>
                                <span class="badge bg-success" style="font-weight: 500;"><i class="fa-solid fa-circle-check me-1"></i> Lunas</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark" style="font-weight: 500;"><i class="fa-solid fa-clock me-1"></i> Belum Lunas</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-accent btn-edit" 
                                        data-id="<?= $row['id'] ?>" 
                                        data-karyawan="<?= $row['id_karyawan'] ?>"
                                        data-tanggal="<?= $row['tanggal'] ?>"
                                        data-nominal="<?= (int)$row['nominal'] ?>"
                                        data-status="<?= $row['status_piutang'] ?>"
                                        data-keterangan="<?= $row['keterangan'] ?>"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="index.php?page=piutang&action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete-confirm btn-icon">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
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
                <h5 class="modal-title text-accent-primary" id="addModalLabel" style="font-weight: 700;">Catat Piutang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=piutang&action=create" method="POST">
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="add-id_karyawan" class="form-label" style="font-weight: 500;">Pilih Karyawan</label>
                        <select class="form-select shadow-none" id="add-id_karyawan" name="id_karyawan" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= $emp['nik'] ?> - <?= $emp['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add-tanggal" class="form-label" style="font-weight: 500;">Tanggal Pinjam</label>
                        <input type="date" class="form-control" id="add-tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-nominal" class="form-label" style="font-weight: 500;">Nominal Pinjaman (Rp)</label>
                        <input type="number" min="1000" class="form-control" id="add-nominal" name="nominal" placeholder="Contoh: 500000" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-keterangan" class="form-label" style="font-weight: 500;">Keterangan / Keperluan</label>
                        <textarea class="form-control" id="add-keterangan" name="keterangan" rows="2" placeholder="Tulis alasan pinjaman... (opsional)"></textarea>
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
                <h5 class="modal-title text-accent-primary" id="editModalLabel" style="font-weight: 700;">Ubah Catatan Piutang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=piutang&action=edit" method="POST">
                <div class="modal-body px-4">
                    <input type="hidden" id="edit-id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit-id_karyawan" class="form-label" style="font-weight: 500;">Karyawan</label>
                        <select class="form-select shadow-none" id="edit-id_karyawan" name="id_karyawan" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= $emp['nik'] ?> - <?= $emp['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-tanggal" class="form-label" style="font-weight: 500;">Tanggal Pinjam</label>
                        <input type="date" class="form-control" id="edit-tanggal" name="tanggal" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-nominal" class="form-label" style="font-weight: 500;">Nominal Pinjaman (Rp)</label>
                        <input type="number" min="1000" class="form-control" id="edit-nominal" name="nominal" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-status_piutang" class="form-label" style="font-weight: 500;">Status Pembayaran</label>
                        <select class="form-select shadow-none" id="edit-status_piutang" name="status_piutang" required>
                            <option value="Belum Lunas">Belum Lunas</option>
                            <option value="Lunas">Lunas</option>
                        </select>
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
                const id = this.getAttribute('data-id');
                const karyawan = this.getAttribute('data-karyawan');
                const tanggal = this.getAttribute('data-tanggal');
                const nominal = this.getAttribute('data-nominal');
                const status = this.getAttribute('data-status');
                const keterangan = this.getAttribute('data-keterangan');

                document.getElementById('edit-id').value = id;
                document.getElementById('edit-id_karyawan').value = karyawan;
                document.getElementById('edit-tanggal').value = tanggal;
                document.getElementById('edit-nominal').value = nominal;
                document.getElementById('edit-status_piutang').value = status;
                document.getElementById('edit-keterangan').value = keterangan;
            });
        });
    });
</script>
