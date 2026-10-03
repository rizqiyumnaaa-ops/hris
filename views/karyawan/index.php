<!-- Karyawan Listing View -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Data Karyawan</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola profil, jabatan, divisi, dan payroll karyawan</p>
            </div>
            <a href="index.php?page=karyawan&action=create" class="btn btn-accent d-flex align-items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Tambah Karyawan
            </a>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-premium mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="page" value="karyawan">
            
            <div class="col-md-3">
                <label for="search" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Pencarian Nama / NIK</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Cari karyawan..." value="<?= $filters['search'] ?>">
            </div>
            
            <div class="col-md-3">
                <label for="id_divisi" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Filter Divisi</label>
                <select class="form-select" id="id_divisi" name="id_divisi">
                    <option value="">-- Semua Divisi --</option>
                    <?php foreach ($divisi as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $filters['id_divisi'] == $d['id'] ? 'selected' : '' ?>><?= $d['nama_divisi'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="id_jabatan" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Filter Jabatan</label>
                <select class="form-select" id="id_jabatan" name="id_jabatan">
                    <option value="">-- Semua Jabatan --</option>
                    <?php foreach ($jabatan as $j): ?>
                    <option value="<?= $j['id'] ?>" <?= $filters['id_jabatan'] == $j['id'] ? 'selected' : '' ?>><?= $j['nama_jabatan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="status_aktif" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Status</label>
                <select class="form-select" id="status_aktif" name="status_aktif">
                    <option value="">-- Semua --</option>
                    <option value="1" <?= $filters['status_aktif'] === '1' ? 'selected' : '' ?>>Aktif</option>
                    <option value="0" <?= $filters['status_aktif'] === '0' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-accent w-100 py-2"><i class="fa-solid fa-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Employees Table Card -->
<div class="card card-premium">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-premium datatable w-100">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Foto</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>Divisi</th>
                        <th>Jabatan</th>
                        <th>Gaji Harian</th>
                        <th>Tarif Lembur</th>
                        <th class="text-center">Status</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($data as $row): 
                        $foto_path = !empty($row['foto']) && file_exists('assets/uploads/foto_karyawan/' . $row['foto']) 
                            ? 'assets/uploads/foto_karyawan/' . $row['foto'] 
                            : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <img src="<?= $foto_path ?>" alt="Foto" class="rounded-circle object-fit-cover" style="width: 42px; height: 42px; border: 2px solid var(--accent-border);">
                        </td>
                        <td><strong><?= $row['nik'] ?></strong></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $row['nama_divisi'] ?></span></td>
                        <td><span class="badge bg-light text-dark border"><?= $row['nama_jabatan'] ?></span></td>
                        <td><strong><?= format_rupiah($row['gaji_pokok']) ?></strong></td>
                        <td><span class="text-muted"><?= format_rupiah($row['tarif_lembur']) ?>/jam</span></td>
                        <td class="text-center">
                            <?php if ($row['status_aktif'] == 1): ?>
                                <span class="badge bg-success" style="font-weight: 500;">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger" style="font-weight: 500;">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-accent dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Pilih Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 12px; border-color: var(--accent-border);">
                                    <li><a class="dropdown-item" href="index.php?page=karyawan&action=detail&id=<?= $row['id'] ?>"><i class="fa-solid fa-eye me-2 text-info"></i> Detail Profil</a></li>
                                    <li><a class="dropdown-item" href="index.php?page=karyawan&action=edit&id=<?= $row['id'] ?>"><i class="fa-solid fa-pen-to-square me-2 text-warning"></i> Edit Karyawan</a></li>
                                    
                                    <?php if ($row['status_aktif'] == 1): ?>
                                        <li><a class="dropdown-item" href="index.php?page=karyawan&action=toggle&id=<?= $row['id'] ?>&status=0"><i class="fa-solid fa-user-slash me-2 text-danger"></i> Nonaktifkan</a></li>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="index.php?page=karyawan&action=toggle&id=<?= $row['id'] ?>&status=1"><i class="fa-solid fa-user-check me-2 text-success"></i> Aktifkan</a></li>
                                    <?php endif; ?>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger btn-delete-confirm" href="index.php?page=karyawan&action=delete&id=<?= $row['id'] ?>"><i class="fa-solid fa-trash-can me-2"></i> Hapus Karyawan</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
