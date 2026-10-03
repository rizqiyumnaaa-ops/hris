<!-- Absensi View - Admin: bulk entry form | Pegawai: read-only milik sendiri -->

<?php if (!isset($is_pegawai_view)) $is_pegawai_view = false; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">
                    <?= $is_pegawai_view ? 'Absensi Saya' : 'Absensi Harian Karyawan' ?>
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    <?= $is_pegawai_view
                        ? 'Lihat status kehadiran Anda pada tanggal yang dipilih.'
                        : 'Kelola absensi harian seluruh karyawan aktif secara kolektif' ?>
                </p>
            </div>
            
            <!-- Date Picker Form -->
            <div class="card border p-2 bg-white" style="border-radius: 12px; border-color: var(--accent-border) !important;">
                <form method="GET" action="index.php" id="form-date-picker" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="page" value="absensi">
                    <label for="tanggal" class="text-muted mb-0 px-2" style="font-size: 0.85rem; font-weight: 600;">TANGGAL:</label>
                    <input type="date" class="form-control border-0 bg-light py-1 px-2" id="tanggal" name="tanggal" value="<?= $tanggal ?>" onchange="document.getElementById('form-date-picker').submit()">
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!$is_pegawai_view): ?>
<!-- ========== TAMPILAN ADMIN: Filter + Form Bulk Input ========== -->
<div class="card card-premium mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="page" value="absensi">
            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
            
            <div class="col-md-4">
                <label for="search" class="form-label" style="font-size: 0.85rem; font-weight: 500;">Cari Nama / NIK</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan kata kunci..." value="<?= $filters['search'] ?>">
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

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-accent w-100 py-2"><i class="fa-solid fa-filter me-2"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Save Form -->
<form action="index.php?page=absensi&action=save" method="POST" id="form-absensi-bulk">
    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
    
    <div class="card card-premium">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h5 class="mb-0 text-accent-primary" style="font-size: 1.05rem; font-weight: 600;">Daftar Absensi - <?= format_tanggal($tanggal) ?></h5>
            <div class="d-flex gap-2">
                <button type="button" id="btn-hadir-semua" class="btn btn-outline-accent d-flex align-items-center gap-2">
                    <i class="fa-solid fa-square-check"></i> Hadir Semua
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($data)): ?>
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486831.png" alt="Empty" style="width: 80px;" class="mb-3 opacity-50">
                    <h5 class="text-muted">Tidak Ada Karyawan Aktif</h5>
                    <p class="text-muted mb-0">Pastikan terdapat karyawan berstatus aktif dalam divisi/jabatan yang dipilih.</p>
                </div>
            <?php else: ?>
                <!-- DESKTOP VIEW (Table) -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-premium w-100 align-middle">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">NIK</th>
                                <th width="20%">Nama Karyawan</th>
                                <th width="15%">Divisi / Jabatan</th>
                                <th width="35%">Status Absensi</th>
                                <th width="18%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($data as $row): 
                                $id_karyawan = $row['id_karyawan'];
                                $current_status = $row['status_absensi'] ?? '';
                                $current_keterangan = $row['keterangan'] ?? '';
                            ?>
                            <tr class="attendance-desktop-row">
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['nik']) ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong>
                                    <?php if (!empty($row['id_absensi'])): ?>
                                        <span class="d-block text-muted" style="font-size: 0.75rem;">
                                            <i class="fa-regular fa-clock me-1"></i> Absen jam: <?= date('H:i', strtotime($row['jam_absen'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
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
                                <td>
                                    <div class="attendance-radio-group">
                                        <?php foreach (['Hadir','Setengah Hari','Izin','Kerja Proyek'] as $st): ?>
                                        <input type="radio"
                                               name="absensi[<?= $id_karyawan ?>][status_absensi]"
                                               value="<?= $st ?>"
                                               id="status_dt_<?= strtolower(str_replace(' ','_',$st)) ?>_<?= $id_karyawan ?>"
                                               class="attendance-radio desktop-radio"
                                               data-id="<?= $id_karyawan ?>"
                                               <?= $current_status === $st ? 'checked' : '' ?>>
                                        <label class="attendance-label" for="status_dt_<?= strtolower(str_replace(' ','_',$st)) ?>_<?= $id_karyawan ?>">
                                            <?= $st === 'Setengah Hari' ? '½ Hari' : ($st === 'Kerja Proyek' ? 'Proyek' : $st) ?>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <input type="text"
                                           name="absensi[<?= $id_karyawan ?>][keterangan]"
                                           class="form-control form-control-sm desktop-ket"
                                           data-id="<?= $id_karyawan ?>"
                                           value="<?= htmlspecialchars($current_keterangan) ?>"
                                           placeholder="Keterangan (opsional)">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE VIEW (Cards) -->
                <div class="row g-3 d-flex d-md-none mt-2">
                    <?php foreach ($data as $row): 
                        $id_karyawan = $row['id_karyawan'];
                        $current_status = $row['status_absensi'] ?? '';
                        $current_keterangan = $row['keterangan'] ?? '';
                    ?>
                    <div class="col-12 attendance-mobile-card" data-id="<?= $id_karyawan ?>">
                        <div class="card p-3 h-100" style="border-radius: 16px; background: rgba(255, 255, 255, 0.8); border: 1px solid rgba(220, 38, 38, 0.1);">
                            
                            <div class="mb-3">
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($row['nik']) ?></div>
                                <div class="fw-bold text-dark" style="font-size: 1.15rem; margin-bottom: 8px;"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                
                                <div style="border-top: 1px dashed rgba(15, 23, 42, 0.1); margin: 8px 0;"></div>
                                
                                <div class="d-flex flex-column gap-2 mt-2">
                                    <div class="d-inline-flex">
                                        <span class="badge border text-dark bg-white" style="font-weight: 500; font-size: 0.75rem; border-radius: 6px; padding: 4px 8px;">
                                            <?= htmlspecialchars($row['nama_divisi'] ?? '') ?>
                                        </span>
                                    </div>
                                    <div class="badge border text-dark bg-white w-100 py-2" style="font-weight: 700; font-size: 0.8rem; border-radius: 6px;">
                                        <?= htmlspecialchars($row['nama_jabatan'] ?? '') ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="fw-bold mb-2" style="font-size: 0.75rem; color: #475569; letter-spacing: 0.5px;">STATUS KEHADIRAN:</div>
                            
                            <div class="attendance-radio-group mb-3" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                                <?php foreach (['Hadir','Setengah Hari','Izin','Kerja Proyek'] as $st): ?>
                                <input type="radio"
                                       name="mobile_absensi_<?= $id_karyawan ?>"
                                       value="<?= $st ?>"
                                       id="status_mb_<?= strtolower(str_replace(' ','_',$st)) ?>_<?= $id_karyawan ?>"
                                       class="attendance-radio mobile-radio"
                                       data-id="<?= $id_karyawan ?>"
                                       <?= $current_status === $st ? 'checked' : '' ?>>
                                <label class="attendance-label bg-white border m-0" for="status_mb_<?= strtolower(str_replace(' ','_',$st)) ?>_<?= $id_karyawan ?>" style="font-size: 0.8rem; border-radius: 6px; padding: 8px 0; text-align: center; cursor: pointer;">
                                    <?= $st === 'Setengah Hari' ? '½ Hari' : ($st === 'Kerja Proyek' ? 'Proyek' : $st) ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <input type="text"
                                   class="form-control mobile-ket"
                                   data-id="<?= $id_karyawan ?>"
                                   value="<?= htmlspecialchars($current_keterangan) ?>"
                                   placeholder="Keterangan (opsional)"
                                   style="border-radius: 8px; font-size: 0.85rem; padding: 10px 12px; background: white; border: 1px solid rgba(220, 38, 38, 0.15);">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-accent px-5 py-2" style="border-radius: 8px; font-weight: 600;">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Absensi
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php else: ?>
<!-- ========== TAMPILAN PEGAWAI: Read-only status absensi sendiri ========== -->

<?php if (empty($data) || empty($data[0]['id_absensi'])): ?>
    <!-- Belum ada absensi hari ini -->
    <div class="card card-premium">
        <div class="card-body text-center py-5">
            <?php if (empty($data)): ?>
                <i class="fa-solid fa-circle-exclamation fa-3x mb-3" style="color: var(--accent-primary); opacity:0.5;"></i>
                <h5 class="text-muted">Data karyawan tidak ditemukan</h5>
                <p class="text-muted">Hubungi admin untuk menghubungkan akun Anda dengan data karyawan.</p>
            <?php else: ?>
                <i class="fa-solid fa-calendar-xmark fa-3x mb-3" style="color: var(--accent-primary); opacity:0.5;"></i>
                <h5 class="text-muted">Belum Ada Absensi</h5>
                <p class="text-muted mb-0">Anda belum memiliki catatan absensi untuk tanggal <strong><?= format_tanggal($tanggal) ?></strong>.</p>
                <p class="text-muted" style="font-size:0.85rem;">Absensi diinput oleh Admin setiap hari.</p>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <?php $row = $data[0]; ?>
    <?php
        $status_colors = [
            'Hadir'        => ['bg' => '#2e7d32', 'icon' => 'fa-circle-check'],
            'Setengah Hari'=> ['bg' => '#0277bd', 'icon' => 'fa-circle-half-stroke'],
            'Izin'         => ['bg' => '#ef6c00', 'icon' => 'fa-circle-minus'],
            'Sakit'        => ['bg' => '#7b1fa2', 'icon' => 'fa-kit-medical'],
            'Alpha'        => ['bg' => '#c62828', 'icon' => 'fa-circle-xmark'],
            'Kerja Proyek' => ['bg' => '#00796b', 'icon' => 'fa-briefcase'],
        ];
        $sc = $status_colors[$row['status_absensi']] ?? ['bg' => '#555', 'icon' => 'fa-circle'];
    ?>
    <div class="card card-premium">
        <div class="card-body">
            <div class="row align-items-center g-4">
                <!-- Status utama -->
                <div class="col-md-4 text-center">
                    <div class="d-inline-flex flex-column align-items-center justify-content-center rounded-circle"
                         style="width:120px;height:120px;background:<?= $sc['bg'] ?>22;border:3px solid <?= $sc['bg'] ?>;">
                        <i class="fa-solid <?= $sc['icon'] ?> fa-2x mb-2" style="color:<?= $sc['bg'] ?>;"></i>
                        <span class="fw-bold" style="color:<?= $sc['bg'] ?>;font-size:0.85rem;"><?= $row['status_absensi'] ?></span>
                    </div>
                    <p class="text-muted mt-2 mb-0" style="font-size:0.8rem;">Status Hari Ini</p>
                </div>
                <!-- Detail -->
                <div class="col-md-8">
                    <h5 class="fw-bold mb-3" style="color:var(--accent-primary);">
                        <i class="fa-solid fa-calendar-day me-2"></i><?= format_tanggal($tanggal) ?>
                    </h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--card-bg);border:1px solid var(--accent-border);">
                                <p class="text-muted mb-1" style="font-size:0.75rem;font-weight:600;">NAMA</p>
                                <p class="mb-0 fw-semibold"><?= htmlspecialchars($row['nama_lengkap']) ?></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--card-bg);border:1px solid var(--accent-border);">
                                <p class="text-muted mb-1" style="font-size:0.75rem;font-weight:600;">NIK</p>
                                <p class="mb-0 fw-semibold"><code><?= htmlspecialchars($row['nik']) ?></code></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--card-bg);border:1px solid var(--accent-border);">
                                <p class="text-muted mb-1" style="font-size:0.75rem;font-weight:600;">JAM ABSEN</p>
                                <p class="mb-0 fw-semibold">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    <?= !empty($row['jam_absen']) ? date('H:i', strtotime($row['jam_absen'])) : '—' ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background:var(--card-bg);border:1px solid var(--accent-border);">
                                <p class="text-muted mb-1" style="font-size:0.75rem;font-weight:600;">DIVISI / JABATAN</p>
                                <p class="mb-0 fw-semibold" style="font-size:0.9rem;">
                                    <?= htmlspecialchars($row['nama_divisi']) ?> / <?= htmlspecialchars($row['nama_jabatan']) ?>
                                </p>
                            </div>
                        </div>
                        <?php if (!empty($row['keterangan'])): ?>
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background:var(--card-bg);border:1px solid var(--accent-border);">
                                <p class="text-muted mb-1" style="font-size:0.75rem;font-weight:600;">KETERANGAN</p>
                                <p class="mb-0"><?= htmlspecialchars($row['keterangan']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php endif; ?>

<script>
<?php if (!$is_pegawai_view): ?>
    // Mapping warna per status absensi
    const statusColors = {
        'Hadir':        { bg: '#2e7d32', color: '#fff', border: '#2e7d32' },
        'Setengah Hari':{ bg: '#0277bd', color: '#fff', border: '#0277bd' },
        'Izin':         { bg: '#ef6c00', color: '#fff', border: '#ef6c00' },
        'Sakit':        { bg: '#7b1fa2', color: '#fff', border: '#7b1fa2' },
        'Alpha':        { bg: '#c62828', color: '#fff', border: '#c62828' },
        'Kerja Proyek': { bg: '#00796b', color: '#fff', border: '#00796b' },
    };

    function highlightRow(radio) {
        const group = radio.closest('.attendance-radio-group');
        if (!group) return;
        group.querySelectorAll('.attendance-label').forEach(lbl => {
            lbl.style.background  = '';
            lbl.style.color       = '';
            lbl.style.borderColor = '';
            lbl.style.fontWeight  = '500';
        });
        const activeLbl = group.querySelector(`label[for="${radio.id}"]`);
        if (activeLbl && statusColors[radio.value]) {
            const c = statusColors[radio.value];
            activeLbl.style.background  = c.bg;
            activeLbl.style.color       = c.color;
            activeLbl.style.borderColor = c.border;
            activeLbl.style.fontWeight  = '600';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.attendance-radio').forEach(radio => {
            if (radio.checked) highlightRow(radio);
            radio.addEventListener('change', function() { highlightRow(this); });
        });

        // Sync Mobile to Desktop
        document.querySelectorAll('.mobile-radio').forEach(mRadio => {
            mRadio.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const val = this.value;
                const dRadio = document.querySelector(`.desktop-radio[data-id="${id}"][value="${val}"]`);
                if (dRadio) {
                    dRadio.checked = true;
                    highlightRow(dRadio);
                }
            });
        });
        document.querySelectorAll('.desktop-radio').forEach(dRadio => {
            dRadio.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const val = this.value;
                const mRadio = document.querySelector(`.mobile-radio[data-id="${id}"][value="${val}"]`);
                if (mRadio) {
                    mRadio.checked = true;
                    highlightRow(mRadio);
                }
            });
        });

        document.querySelectorAll('.mobile-ket').forEach(mKet => {
            mKet.addEventListener('input', function() {
                const id = this.getAttribute('data-id');
                const dKet = document.querySelector(`.desktop-ket[data-id="${id}"]`);
                if (dKet) dKet.value = this.value;
            });
        });
        document.querySelectorAll('.desktop-ket').forEach(dKet => {
            dKet.addEventListener('input', function() {
                const id = this.getAttribute('data-id');
                const mKet = document.querySelector(`.mobile-ket[data-id="${id}"]`);
                if (mKet) mKet.value = this.value;
            });
        });

        const btnHadirSemua = document.getElementById('btn-hadir-semua');
        if (btnHadirSemua) {
            btnHadirSemua.addEventListener('click', function() {
                document.querySelectorAll('input[type="radio"][value="Hadir"]').forEach(radio => {
                    radio.checked = true;
                    highlightRow(radio);
                });
                Swal.fire({
                    icon: 'success', title: 'Semua Hadir!',
                    text: 'Seluruh karyawan diset "Hadir". Jangan lupa klik Simpan Absensi!',
                    confirmButtonColor: '#dc2626', timer: 2000, toast: true,
                    position: 'top-end', showConfirmButton: false
                });
            });
        }

        const formAbsensi = document.getElementById('form-absensi-bulk');
        if (formAbsensi) {
            formAbsensi.addEventListener('submit', function(e) {
                // Check desktop table rows for validation (since they hold the actual submitted data)
                const rows = document.querySelectorAll('.attendance-desktop-row');
                let incomplete = false;
                
                rows.forEach(row => {
                    const radios = row.querySelectorAll('.desktop-radio');
                    if (radios.length > 0) {
                        let checked = false;
                        radios.forEach(r => {
                            if (r.checked) checked = true;
                        });
                        
                        const id = radios[0].getAttribute('data-id');
                        const mobileCard = document.querySelector(`.attendance-mobile-card[data-id="${id}"] .card`);

                        if (!checked) {
                            incomplete = true;
                            row.style.backgroundColor = 'rgba(220, 38, 38, 0.05)'; 
                            if (mobileCard) {
                                mobileCard.style.border = '2px solid #dc2626';
                                mobileCard.style.backgroundColor = 'rgba(220, 38, 38, 0.05)';
                            }
                        } else {
                            row.style.backgroundColor = '';
                            if (mobileCard) {
                                mobileCard.style.border = '1px solid rgba(220, 38, 38, 0.1)';
                                mobileCard.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                            }
                        }
                    }
                });
                
                if (incomplete) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Absensi Belum Lengkap',
                        text: 'Silakan pilih status absensi (Hadir, ½ Hari, Izin, atau Proyek) untuk semua karyawan terlebih dahulu.',
                        confirmButtonColor: '#dc2626'
                    });
                    return false;
                }
                
                $('#loader-overlay').fadeIn(100);
            });
        }
    });
<?php endif; ?>
</script>
