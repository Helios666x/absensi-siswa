<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/helper.php';

check_role([ROLE_ADMIN]);

 $page_title = "Master Jadwal Pelajaran";
require_once __DIR__ . '/../includes/head.php';

// Mengurutkan berdasarkan urutan hari yang benar
 $queryJadwal = "SELECT j.*, k.nama_kelas, g.nama AS nama_guru 
                FROM jadwal j 
                JOIN kelas k ON j.kelas_id = k.id 
                JOIN guru g ON j.guru_id = g.id 
                ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), j.jam_mulai ASC";
 $resultJadwal = $mysqli->query($queryJadwal);

// Data untuk dropdown
 $resultKelas = $mysqli->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC");
 $resultGuru = $mysqli->query("SELECT id, nama, nip FROM guru WHERE status_aktif = 1 ORDER BY nama ASC");

 $success = get_flashdata('success');
 $error = get_flashdata('error');

// Mengelompokkan data berdasarkan hari
 $jadwalPerHari = [];
while ($j = $resultJadwal->fetch_assoc()) {
    $jadwalPerHari[$j['hari']][] = $j;
}
 $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>
<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/../includes/sidebar-admin.php'; ?>
        
        <div class="main-content">
            <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
            
            <div class="content">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert"><?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Data Jadwal Pelajaran</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalJadwal" onclick="resetForm()">
                        <i class="fas fa-plus me-1"></i> Tambah Jadwal
                    </button>
                </div>

                <?php foreach ($hariList as $hari): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0"><i class="fas fa-clock me-2 text-secondary"></i><?= $hari ?></h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (isset($jadwalPerHari[$hari])): ?>
                        <table class="table table-hover align-middle table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Jam</th>
                                    <th width="25%">Kelas</th>
                                    <th width="30%">Kategori / Kegiatan</th>
                                    <th width="20%">Guru Pengampu</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jadwalPerHari[$hari] as $jadwal): ?>
                                    <tr>
                                        <td><?= substr($jadwal['jam_mulai'], 0, 5) ?> - <?= substr($jadwal['jam_selesai'], 0, 5) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($jadwal['nama_kelas']) ?></span></td>
                                        <td><strong><?= htmlspecialchars($jadwal['nama_kegiatan']) ?></strong></td>
                                        <td><?= htmlspecialchars($jadwal['nama_guru']) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                    data-id="<?= $jadwal['id'] ?>" 
                                                    data-hari="<?= $jadwal['hari'] ?>" 
                                                    data-kelas="<?= $jadwal['kelas_id'] ?>" 
                                                    data-guru="<?= $jadwal['guru_id'] ?>"
                                                    data-mulai="<?= $jadwal['jam_mulai'] ?>" 
                                                    data-selesai="<?= $jadwal['jam_selesai'] ?>"
                                                    data-kegiatan="<?= $jadwal['nama_kegiatan'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalJadwal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>actions/admin/jadwal-hapus.php?id=<?= $jadwal['id'] ?>" 
                                               class="btn btn-danger btn-sm" onclick="return confirm('Hapus jadwal ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p class="text-center text-muted py-3 mb-0">Tidak ada jadwal.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php require_once __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>

    <!-- Modal Tambah/Edit Jadwal -->
    <div class="modal fade" id="modalJadwal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASE_URL ?>actions/admin/jadwal-simpan.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Jadwal Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="inputId">
                        
                        <div class="mb-3">
                            <label class="form-label">Hari</label>
                            <select class="form-select" name="hari" id="inputHari" required>
                                <?php foreach($hariList as $h): ?>
                                    <option value="<?= $h ?>"><?= $h ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select class="form-select" name="kelas_id" id="inputKelas" required>
                                <option value="">Pilih Kelas...</option>
                                <?php while($k = $resultKelas->fetch_assoc()): ?>
                                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" name="jam_mulai" id="inputMulai" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" name="jam_selesai" id="inputSelesai" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori / Nama Kegiatan</label>
                            <input type="text" class="form-control" name="nama_kegiatan" id="inputKegiatan" required placeholder="Contoh: Matematika, Fisika, Sholat Dhuha">
                            <div class="form-text">Ini akan menjadi label kategori absensi kelas.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru Pengampu</label>
                            <select class="form-select" name="guru_id" id="inputGuru" required>
                                <option value="">Pilih Guru...</option>
                                <?php while($g = $resultGuru->fetch_assoc()): ?>
                                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama']) ?> (<?= htmlspecialchars($g['nip']) ?>)</option>
                                <?php endwhile; ?>
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
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = 'Edit Jadwal';
                document.getElementById('inputId').value = this.dataset.id;
                document.getElementById('inputHari').value = this.dataset.hari;
                document.getElementById('inputKelas').value = this.dataset.kelas;
                document.getElementById('inputGuru').value = this.dataset.guru;
                document.getElementById('inputMulai').value = this.dataset.mulai;
                document.getElementById('inputSelesai').value = this.dataset.selesai;
                document.getElementById('inputKegiatan').value = this.dataset.kegiatan;
            });
        });

        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Tambah Jadwal Baru';
            document.getElementById("modalJadwal").querySelector('form').reset();
            document.getElementById('inputId').value = '';
        }
    </script>
</body>
</html>