<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/helper.php';

check_role([ROLE_ADMIN]);

 $page_title = "Master Data Siswa";
require_once __DIR__ . '/../includes/head.php';

// Ambil data siswa beserta nama kelasnya
 $querySiswa = "SELECT s.*, k.nama_kelas, u.username 
               FROM siswa s 
               JOIN kelas k ON s.id_kelas = k.id 
               LEFT JOIN users u ON u.related_id = s.id AND u.role = 'siswa' 
               ORDER BY k.nama_kelas ASC, s.nama ASC";
 $resultSiswa = $mysqli->query($querySiswa);

// Ambil data kelas untuk dropdown
 $queryKelas = "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
 $resultKelas = $mysqli->query($queryKelas);

 $success = get_flashdata('success');
 $error = get_flashdata('error');
 $info_akun = get_flashdata('info_akun');
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
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Detail Akun Login Siswa Baru:</h6>
                        <hr>
                        <p class="mb-0"><?= $info_akun ?></p>
                        <small class="text-muted">Segera berikan username dan password ini ke siswa yang bersangkutan.</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-graduate me-2 text-primary"></i>Data Siswa</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalSiswa" onclick="resetForm()">
                            <i class="fas fa-plus me-1"></i> Tambah Siswa
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>L/P</th>
                                        <th>Akun Login</th>
                                        <th width="180">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($resultSiswa->num_rows > 0): ?>
                                        <?php $no = 1; while ($siswa = $resultSiswa->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($siswa['nis']) ?></td>
                                                <td><strong><?= htmlspecialchars($siswa['nama']) ?></strong></td>
                                                <td><span class="badge bg-secondary"><?= htmlspecialchars($siswa['nama_kelas']) ?></span></td>
                                                <td><?= $siswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                                <td>
                                                    <?php if ($siswa['username']): ?>
                                                        <span class="badge bg-success"><?= htmlspecialchars($siswa['username']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">Belum ada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                            data-id="<?= $siswa['id'] ?>" 
                                                            data-nis="<?= $siswa['nis'] ?>" 
                                                            data-nama="<?= $siswa['nama'] ?>" 
                                                            data-kelas="<?= $siswa['id_kelas'] ?>" 
                                                            data-jk="<?= $siswa['jenis_kelamin'] ?>"
                                                            data-bs-toggle="modal" data-bs-target="#modalSiswa">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <a href="<?= BASE_URL ?>actions/admin/siswa-hapus.php?id=<?= $siswa['id'] ?>" 
                                                       class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-3">Belum ada data siswa.</td>
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

    <!-- Modal Tambah/Edit Siswa -->
    <div class="modal fade" id="modalSiswa" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>actions/admin/siswa-simpan.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Siswa Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="inputId">
                        <div class="mb-3">
                            <label for="inputNis" class="form-label">NIS</label>
                            <input type="text" class="form-control" id="inputNis" name="nis" required placeholder="Nomor Induk Siswa">
                        </div>
                        <div class="mb-3">
                            <label for="inputNama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="inputNama" name="nama" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-7">
                                <label for="inputKelas" class="form-label">Kelas</label>
                                <select class="form-select" id="inputKelas" name="id_kelas" required>
                                    <option value="">Pilih Kelas...</option>
                                    <?php if ($resultKelas->num_rows > 0): ?>
                                        <?php while ($kelas = $resultKelas->fetch_assoc()): ?>
                                            <option value="<?= $kelas['id'] ?>"><?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Belum ada data kelas</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-5">
                                <label for="inputJk" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="inputJk" name="jenis_kelamin" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-muted small fst-italic">
                            * Akun login akan dibuat otomatis (Username: siswa_[NIS], Password: siswa123).
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
                document.getElementById('modalTitle').innerText = 'Edit Data Siswa';
                document.getElementById('inputId').value = this.dataset.id;
                document.getElementById('inputNis').value = this.dataset.nis;
                document.getElementById('inputNama').value = this.dataset.nama;
                document.getElementById('inputKelas').value = this.dataset.kelas;
                document.getElementById('inputJk').value = this.dataset.jk;
            });
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Siswa Baru';
            document.getElementById('inputId').value = '';
            document.getElementById('inputNis').value = '';
            document.getElementById('inputNama').value = '';
            document.getElementById('inputKelas').value = '';
            document.getElementById('inputJk').value = 'L';
        }
    </script>
</body>
</html>