<!-- Divisi Management View -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-accent-primary" style="font-weight: 700;">Data Divisi</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola divisi / departemen kerja karyawan</p>
            </div>
            <button type="button" class="btn btn-accent d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus"></i> Tambah Divisi
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-premium">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-premium datatable w-100">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th>Nama Divisi</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($data as $row): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $row['nama_divisi'] ?></strong></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-accent btn-edit" 
                                                data-id="<?= $row['id'] ?>" 
                                                data-nama="<?= $row['nama_divisi'] ?>"
                                                data-bs-toggle="modal" data-bs-target="#editModal">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                        <a href="index.php?page=divisi&action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-delete-confirm btn-icon">
                                            <i class="fa-solid fa-trash"></i> Hapus
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
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title text-accent-primary" id="addModalLabel" style="font-weight: 700;">Tambah Divisi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=divisi&action=create" method="POST">
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="nama_divisi" class="form-label" style="font-weight: 500;">Nama Divisi</label>
                        <input type="text" class="form-control" id="nama_divisi" name="nama_divisi" placeholder="Masukkan nama divisi" required>
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
                <h5 class="modal-title text-accent-primary" id="editModalLabel" style="font-weight: 700;">Ubah Nama Divisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=divisi&action=edit" method="POST">
                <div class="modal-body px-4">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="edit-nama_divisi" class="form-label" style="font-weight: 500;">Nama Divisi</label>
                        <input type="text" class="form-control" id="edit-nama_divisi" name="nama_divisi" required>
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
        // Event handler for Edit button to populate modal fields
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-nama_divisi').value = nama;
            });
        });
    });
</script>
