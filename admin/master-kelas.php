<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/helper.php';

check_role([ROLE_ADMIN]);

 $page_title = "Master Kelas & Kategori";
require_once __DIR__ . '/../includes/head.php';

// Ambil data kelas beserta nama wali kelasnya
 $queryKelas = "SELECT k.*, g.nama AS nama_guru FROM kelas k LEFT JOIN guru g ON k.guru_id = g.id ORDER BY k.tingkat ASC, k.nama_kelas ASC";
 $resultKelas = $mysqli->query($queryKelas);

// Ambil data guru untuk dropdown wali kelas
 $queryGuru = "SELECT id, nama, nip FROM guru WHERE status_aktif = 1 ORDER BY nama ASC";
 $resultGuru = $mysqli->query($queryGuru);

 $success = get_flashdata('success');
 $error = get_flashdata('error');
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

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-school me-2 text-primary"></i>Data Kelas</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKelas" onclick="resetForm()">
                            <i class="fas fa-plus me-1"></i> Tambah Kelas
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kelas</th>
                                        <th>Tingkat</th>
                                        <th>Kategori</th>
                                        <th>Wali Kelas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($resultKelas->num_rows > 0): ?>
                                        <?php $no = 1; while ($kelas = $resultKelas->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><strong><?= htmlspecialchars($kelas['nama_kelas']) ?></strong></td>
                                                <td><span class="badge bg-secondary"><?= htmlspecialchars($kelas['tingkat']) ?></span></td>
                                                <td><?= htmlspecialchars($kelas['kategori']) ?></td>
                                                <td><?= $kelas['nama_guru'] ? htmlspecialchars($kelas['nama_guru']) : '<span class="text-muted">Belum ditentukan</span>' ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                            data-id="<?= $kelas['id'] ?>" 
                                                            data-nama="<?= $kelas['nama_kelas'] ?>" 
                                                            data-tingkat="<?= $kelas['tingkat'] ?>" 
                                                            data-kategori="<?= $kelas['kategori'] ?>" 
                                                            data-guru="<?= $kelas['guru_id'] ?>"
                                                            data-bs-toggle="modal" data-bs-target="#modalKelas">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <a href="<?= BASE_URL ?>actions/admin/kelas-hapus.php?id=<?= $kelas['id'] ?>" 
                                                       class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">Belum ada data kelas.</td>
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

    <!-- Modal Tambah/Edit Kelas -->
    <div class="modal fade" id="modalKelas" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>actions/admin/kelas-simpan.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Kelas Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="inputId">
                        
                        <div class="mb-3">
                            <label for="inputNama" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="inputNama" name="nama_kelas" required placeholder="Contoh: XII RPL 1">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="inputTingkat" class="form-label">Tingkat</label>
                                <select class="form-select" id="inputTingkat" name="tingkat" required>
                                    <option value="">Pilih...</option>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="inputKategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="inputKategori" name="kategori" required placeholder="Contoh: IPA, IPS">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="inputGuru" class="form-label">Wali Kelas</label>
                            <select class="form-select" id="inputGuru" name="guru_id">
                                <option value="">-- Tanpa Wali Kelas --</option>
                                <?php if ($resultGuru->num_rows > 0): ?>
                                    <?php while ($guru = $resultGuru->fetch_assoc()): ?>
                                        <option value="<?= $guru['id'] ?>"><?= htmlspecialchars($guru['nama']) ?> (<?= htmlspecialchars($guru['nip']) ?>)</option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="" disabled>Belum ada data guru</option>
                                <?php endif; ?>
                            </select>
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
        // Fungsi untuk mengisi form modal saat tombol Edit diklik
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = 'Edit Data Kelas';
                document.getElementById('inputId').value = this.dataset.id;
                document.getElementById('inputNama').value = this.dataset.nama;
                document.getElementById('inputTingkat').value = this.dataset.tingkat;
                document.getElementById('inputKategori').value = this.dataset.kategori;
                document.getElementById('inputGuru').value = this.dataset.guru;
            });
        });

        // Fungsi reset form saat tombol Tambah diklik
        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Kelas Baru';
            document.getElementById('inputId').value = '';
            document.getElementById('inputNama').value = '';
            document.getElementById('inputTingkat').value = '';
            document.getElementById('inputKategori').value = '';
            document.getElementById('inputGuru').value = '';
        }
    </script>
</body>
</html>