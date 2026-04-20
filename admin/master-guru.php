<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/helper.php';

check_role([ROLE_ADMIN]);

 $page_title = "Master Data Guru";
require_once __DIR__ . '/../includes/head.php';

// Query menggabungkan tabel guru dan users untuk mendapatkan username login
 $queryGuru = "SELECT g.*, u.username, u.status_aktif AS status_akun 
              FROM guru g 
              LEFT JOIN users u ON u.related_id = g.id AND u.role = 'guru' 
              ORDER BY g.nama ASC";
 $resultGuru = $mysqli->query($queryGuru);

 $success = get_flashdata('success');
 $error = get_flashdata('error');
// Untuk menampilkan info akun yang baru dibuat
 $info_akun = get_flashdata('info_akun'); 
?>
<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/../includes/sidebar-admin.php'; ?>
        
        <div class="main-content">
            <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
            
            <div class="content">
                <!-- Notifikasi -->
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
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Detail Akun Login Guru Baru:</h6>
                        <hr>
                        <p class="mb-0"><?= $info_akun ?></p>
                        <small class="text-muted">Segera berikan username dan password ini ke guru yang bersangkutan.</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Data Guru</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalGuru" onclick="resetForm()">
                            <i class="fas fa-plus me-1"></i> Tambah Guru
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>NIP</th>
                                        <th>Nama Guru</th>
                                        <th>Mapel</th>
                                        <th>No. HP</th>
                                        <th>Akun Login</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($resultGuru->num_rows > 0): ?>
                                        <?php $no = 1; while ($guru = $resultGuru->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($guru['nip']) ?></td>
                                                <td><strong><?= htmlspecialchars($guru['nama']) ?></strong></td>
                                                <td><?= htmlspecialchars($guru['mapel']) ?: '-' ?></td>
                                                <td><?= htmlspecialchars($guru['no_hp']) ?: '-' ?></td>
                                                <td>
                                                    <?php if ($guru['username']): ?>
                                                        <span class="badge bg-success"><?= htmlspecialchars($guru['username']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">Belum ada akun</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                            data-id="<?= $guru['id'] ?>" 
                                                            data-nip="<?= $guru['nip'] ?>" 
                                                            data-nama="<?= $guru['nama'] ?>" 
                                                            data-mapel="<?= $guru['mapel'] ?>" 
                                                            data-hp="<?= $guru['no_hp'] ?>"
                                                            data-bs-toggle="modal" data-bs-target="#modalGuru">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <a href="<?= BASE_URL ?>actions/admin/guru-hapus.php?id=<?= $guru['id'] ?>" 
                                                       class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus guru ini? Akun login-nya juga akan dihapus.')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-3">Belum ada data guru.</td>
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

    <!-- Modal Tambah/Edit Guru -->
    <div class="modal fade" id="modalGuru" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>actions/admin/guru-simpan.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Guru Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="inputId">
                        <div class="mb-3">
                            <label for="inputNip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="inputNip" name="nip" required placeholder="Nomor Induk Pegawai">
                        </div>
                        <div class="mb-3">
                            <label for="inputNama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="inputNama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputMapel" class="form-label">Mata Pelajaran</label>
                            <input type="text" class="form-control" id="inputMapel" name="mapel" placeholder="Contoh: Matematika">
                        </div>
                        <div class="mb-3">
                            <label for="inputHp" class="form-label">No. HP</label>
                            <input type="tel" class="form-control" id="inputHp" name="no_hp" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="text-muted small fst-italic">
                            * Akun login akan dibuat secara otomatis saat menambah guru baru (Username: guru_[NIP], Password: guru123).
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
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = 'Edit Data Guru';
                document.getElementById('inputId').value = this.dataset.id;
                document.getElementById('inputNip').value = this.dataset.nip;
                document.getElementById('inputNama').value = this.dataset.nama;
                document.getElementById('inputMapel').value = this.dataset.mapel;
                document.getElementById('inputHp').value = this.dataset.hp;
            });
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Guru Baru';
            document.getElementById('inputId').value = '';
            document.getElementById('inputNip').value = '';
            document.getElementById('inputNama').value = '';
            document.getElementById('inputMapel').value = '';
            document.getElementById('inputHp').value = '';
        }
    </script>
</body>
</html>