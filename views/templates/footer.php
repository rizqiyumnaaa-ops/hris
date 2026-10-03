            </div> <!-- Close page-content -->
            
            <!-- Footer -->
            <footer class="text-center py-3 mt-auto no-print" style="background: rgba(255, 255, 255, 0.25); border-top: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                <span class="text-muted" style="font-size: 0.85rem; font-weight: 500;">&copy; <?= date('Y') ?> Sistem Presensi & Penggajian Karyawan. All Rights Reserved.</span>
            </footer>
        </div> <!-- Close content -->
    </div> <!-- Close wrapper -->

    <!-- Sidebar Mobile Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Custom Main JS -->
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('.form-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                minimumResultsForSearch: 10
            });
            // Hide Loader
            $('#loader-overlay').fadeOut(400);

            // Sidebar Toggle & Overlay Close on Mobile
            $('#sidebarCollapse, #sidebarOverlay').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#sidebarOverlay').toggleClass('show');
            });

            // Initialize DataTables
            if ($('.datatable').length > 0) {
                $('.datatable').each(function() {
                    var $el = $(this);
                    var searching = $el.data('searching') !== false;
                    var lengthChange = $el.data('length-change') !== false;
                    
                    var dt = $el.DataTable({
                        searching: searching,
                        lengthChange: lengthChange,
                        drawCallback: function(settings) {
                            var api = this.api();
                            var headers = api.columns().header().toArray().map(th => $(th).text().trim());
                            api.rows({page:'current'}).nodes().each(function(row, i) {
                                $(row).find('td').each(function(j) {
                                    if(headers[j]) {
                                        $(this).attr('data-label', headers[j]);
                                    }
                                });
                            });
                        },
                        language: {
                            decimal: "",
                            emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                            infoFiltered: "(disaring dari _MAX_ total entri)",
                            infoPostFix: "",
                            thousands: ",",
                            lengthMenu: "Tampilkan _MENU_ entri",
                            loadingRecords: "Sedang memuat...",
                            processing: "Sedang memproses...",
                            search: "Cari:",
                            zeroRecords: "Tidak ditemukan data yang sesuai",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                              },
                            aria: {
                                sortAscending: ": aktifkan untuk mengurutkan kolom ke atas",
                                sortDescending: ": aktifkan untuk mengurutkan kolom ke bawah"
                            }
                        }
                    });
                });
            }

            // Inject data-label for regular tables (non-datatable)
            $('.table-premium:not(.dataTable)').each(function() {
                var headers = $(this).find('thead th').map(function() {
                    return $(this).text().trim();
                }).get();
                $(this).find('tbody tr').each(function() {
                    $(this).find('td').each(function(i) {
                        if (headers[i]) $(this).attr('data-label', headers[i]);
                    });
                });
            });

            // Confirm Delete SweetAlert2
            $(document).on('click', '.btn-delete-confirm', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#7c7267',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loader-overlay').fadeIn(100);
                        window.location.href = url;
                    }
                });
            });

            // Logout Confirmation
            $('#btn-logout').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Sesi admin Anda akan berakhir.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#7c7267',
                    confirmButtonText: 'Ya, Keluar!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loader-overlay').fadeIn(100);
                        window.location.href = 'index.php?page=logout';
                    }
                });
            });
            
            // Auto fade alert boxes
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 4000);
        });

        // Trigger flash messages if present
        <?php
        $flash = get_flash_message();
        if ($flash):
        ?>
        Swal.fire({
            icon: '<?= $flash['type'] ?>',
            title: '<?= $flash['title'] ?>',
            text: '<?= $flash['text'] ?>',
            confirmButtonColor: '#dc2626',
            timer: 3500,
            timerProgressBar: true
        });
        <?php endif; ?>
    </script>
</body>
</html>
