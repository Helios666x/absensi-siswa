<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/helper.php';

check_role([ROLE_ADMIN]);

 $page_title = "Master Device ESP32";
require_once __DIR__ . '/../includes/head.php';

 $queryDevice = "SELECT * FROM device ORDER BY lokasi ASC";
 $resultDevice = $mysqli->query($queryDevice);

 $success = get_flashdata('success');
 $error = get_flashdata('error');
 $info_akun = get_flashdata('info_akun'); // Digunakan untuk menampilkan API Key baru
?>
<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/../includes/sidebar-admin.php'; ?>
        
        <div class="main-content">
            <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
            
            <div class="content">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($info_akun): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-key me-2"></i>API Key Baru:</h6>
                        <hr>
                        <input type="text" class="form-control bg-light" value="<?= $info_akun ?>" id="apiKeyText" readonly>
                        <small class="text-muted">Salin API Key ini, ini hanya ditampilkan sekali! Masukkan ke kode ESP32.</small>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="copyKey()">Salin Key</button>
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-microchip me-2 text-primary"></i>Daftar Perangkat ESP32</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDevice" onclick="resetForm()">
                            <i class="fas fa-plus me-1"></i> Tambah Device
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Device</th>
                                        <th>Lokasi / Ruangan</th>
                                        <th>API Key</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($resultDevice->num_rows > 0): ?>
                                        <?php $no = 1; while ($device = $resultDevice->fetch_assoc()): 
                                            // Cek status online (misal heartbeat kurang dari 2 menit)
                                            $is_online = ($device['last_heartbeat'] && (time() - strtotime($device['last_heartbeat']) < 120));
                                        ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><strong><?= htmlspecialchars($device['nama_device']) ?></strong></td>
                                                <td><?= htmlspecialchars($device['lokasi']) ?></td>
                                                <td><code><?= substr($device['api_key'], 0, 10) ?>...</code></td>
                                                <td>
                                                    <?php if ($is_online): ?>
                                                        <span class="badge bg-success"><i class="fas fa-circle fa-xs me-1"></i>Online</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><i class="fas fa-circle fa-xs me-1"></i>Offline</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                            data-id="<?= $device['id'] ?>" 
                                                            data-nama="<?= $device['nama_device'] ?>" 
                                                            data-lokasi="<?= $device['lokasi'] ?>"
                                                            data-bs-toggle="modal" data-bs-target="#modalDevice">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <a href="<?= BASE_URL ?>actions/admin/perangkat-hapus.php?id=<?= $device['id'] ?>" 
                                                       class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus device ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">Belum ada perangkat terdaftar.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php require_once __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>

    <!-- Modal Tambah/Edit Device -->
    <div class="modal fade" id="modalDevice" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>actions/admin/perangkat-simpan.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Device Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="inputId">
                        <div class="mb-3">
                            <label for="inputNama" class="form-label">Nama Device</label>
                            <input type="text" class="form-control" id="inputNama" name="nama_device" required placeholder="Contoh: ESP32 Gerbang Depan">
                        </div>
                        <div class="mb-3">
                            <label for="inputLokasi" class="form-label">Lokasi / Ruangan</label>
                            <input type="text" class="form-control" id="inputLokasi" name="lokasi" required placeholder="Contoh: Gerbang Utama, Ruang X IPA 1">
                        </div>
                        <div class="text-muted small fst-italic">
                            * API Key akan dibuat secara otomatis dan aman oleh sistem saat disimpan.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copyKey() {
            const copyText = document.getElementById("apiKeyText");
            copyText.select();
            document.execCommand("copy");
            alert("API Key berhasil disalin!");
        }

        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = 'Edit Data Device';
                document.getElementById('inputId').value = this.dataset.id;
                document.getElementById('inputNama').value = this.dataset.nama;
                document.getElementById('inputLokasi').value = this.dataset.lokasi;
            });
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Device Baru';
            document.getElementById('inputId').value = '';
            document.getElementById('inputNama').value = '';
            document.getElementById('inputLokasi').value = '';
        }
    </script>
</body>
</html>