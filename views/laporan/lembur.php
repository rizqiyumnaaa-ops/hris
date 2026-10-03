<!-- views/laporan/lembur.php -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Laporan Lembur Karyawan</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Filter dan ekspor pencatatan lembur beserta nominal kompensasi komulatif</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-premium mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="page" value="laporan">
            <input type="hidden" name="action" value="lembur">
            
            <div class="col-md-3">
                <label for="start_date" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $filters['start_date'] ?>">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $filters['end_date'] ?>">
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

            <div class="col-md-3">
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
                <a href="index.php?page=laporan&action=lembur&export=pdf&<?= http_build_query($filters) ?>" target="_blank" class="btn btn-outline-accent py-2"><i class="fa-solid fa-print"></i> PDF</a>
                <a href="index.php?page=laporan&action=lembur&export=excel&<?= http_build_query($filters) ?>" class="btn btn-success py-2 text-white" style="background-color: #2e7d32; border: none;"><i class="fa-solid fa-file-excel"></i> Excel</a>
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
                        <th>Tanggal</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Divisi / Jabatan</th>
                        <th>Jam Lembur</th>
                        <th>Tarif Lembur</th>
                        <th>Total Kompensasi</th>
                        <th>Keterangan</th>
                        <th class="text-center no-print" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $grand_total = 0;
                    foreach ($data as $row): 
                        $grand_total += $row['total_lembur'];
                    ?>
                    <tr>
                        <td data-label="No"><?= $no++ ?></td>
                        <td data-label="Tanggal"><strong><?= format_tanggal($row['tanggal']) ?></strong></td>
                        <td data-label="NIK"><?= $row['nik'] ?></td>
                        <td data-label="Nama Karyawan"><?= $row['nama_lengkap'] ?></td>
                        <td data-label="Divisi / Jabatan">
                            <div class="d-flex flex-column align-items-start gap-1">
                                <span class="badge-divisi">
                                    <i class="fa-solid fa-building me-1 opacity-75"></i><?= htmlspecialchars($row['nama_divisi'] ?? '') ?>
                                </span>
                                <span class="badge-jabatan">
                                    <i class="fa-solid fa-user-tag me-1 opacity-75"></i><?= htmlspecialchars($row['nama_jabatan'] ?? '') ?>
                                </span>
                            </div>
                        </td>
                        <td data-label="Jam Lembur"><strong><?= number_format($row['jumlah_jam'], 1, ',', '.') ?> Jam</strong></td>
                        <td data-label="Tarif Lembur"><?= format_rupiah($row['tarif_lembur']) ?></td>
                        <td data-label="Total Kompensasi"><strong class="text-success"><?= format_rupiah($row['total_lembur']) ?></strong></td>
                        <td data-label="Keterangan"><span class="text-muted" style="font-size: 0.85rem;"><?= !empty($row['keterangan']) ? $row['keterangan'] : '-' ?></span></td>
                        <td data-label="Aksi" class="text-center no-print">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-accent btn-edit" 
                                        data-id="<?= $row['id'] ?>" 
                                        data-karyawan="<?= $row['id_karyawan'] ?>"
                                        data-tanggal="<?= $row['tanggal'] ?>"
                                        data-jam="<?= (float)$row['jumlah_jam'] ?>"
                                        data-keterangan="<?= $row['keterangan'] ?>"
                                        data-bs-toggle="modal" data-bs-target="#editModal" title="Ubah Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="index.php?page=laporan&action=lembur&action=delete&id=<?= $row['id'] ?>&redirect=laporan" class="btn btn-sm btn-danger btn-delete-confirm" title="Hapus Data">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php if (!empty($data)): ?>
                <tfoot>
                    <tr class="table-light font-weight-bold" style="border-top: 2px solid var(--accent-border);">
                        <td colspan="7" class="text-end"><strong>TOTAL KESELURUHAN KOMPENSASI:</strong></td>
                        <td><strong class="text-success"><?= format_rupiah($grand_total) ?></strong></td>
                        <td></td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
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
                    <input type="hidden" name="redirect" value="laporan">
                    
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
                        <label for="edit-tanggal" class="form-label" style="font-weight: 500;">Tanggal Lembur</label>
                        <input type="date" class="form-control" id="edit-tanggal" name="tanggal" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-jumlah_jam" class="form-label" style="font-weight: 500;">Jumlah Jam</label>
                        <input type="number" step="0.5" min="0.5" class="form-control" id="edit-jumlah_jam" name="jumlah_jam" required>
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
                const jam = this.getAttribute('data-jam');
                const keterangan = this.getAttribute('data-keterangan');

                document.getElementById('edit-id').value = id;
                document.getElementById('edit-id_karyawan').value = karyawan;
                document.getElementById('edit-tanggal').value = tanggal;
                document.getElementById('edit-jumlah_jam').value = jam;
                document.getElementById('edit-keterangan').value = keterangan;
            });
        });
    });
</script>
