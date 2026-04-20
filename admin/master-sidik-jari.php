<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/helper.php';

check_role([ROLE_ADMIN]);

 $page_title = "Pendaftaran Sidik Jari";
require_once __DIR__ . '/../includes/head.php';

// Ambil data sidik jari yang sudah terdaftar
 $querySidik = "SELECT sj.*, 
               CASE WHEN sj.tipe_user='guru' THEN g.nama ELSE s.nama END AS nama_user,
               CASE WHEN sj.tipe_user='guru' THEN g.nip ELSE s.nis END AS nomor_induk,
               CASE WHEN sj.tipe_user='guru' THEN 'Guru' ELSE k.nama_kelas END AS keterangan
               FROM sidik_jari sj
               LEFT JOIN guru g ON sj.tipe_user='guru' AND sj.user_id = g.id
               LEFT JOIN siswa s ON sj.tipe_user='siswa' AND sj.user_id = s.id
               LEFT JOIN kelas k ON s.id_kelas = k.id
               WHERE sj.status = 'active'
               ORDER BY sj.fingerprint_id ASC";
 $resultSidik = $mysqli->query($querySidik);

// Data untuk dropdown
 $resultGuru = $mysqli->query("SELECT id, nip, nama FROM guru WHERE status_aktif = 1 ORDER BY nama ASC");
 $resultKelas = $mysqli->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC");

 $success = get_flashdata('success');
 $error = get_flashdata('error');
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

                <!-- Form Pendaftaran -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-fingerprint me-2 text-primary"></i>Daftarkan Sidik Jari Baru</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>actions/admin/sidik-jari-simpan.php" method="POST" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tipe User</label>
                                <select class="form-select" id="tipeUser" name="tipe_user" required>
                                    <option value="siswa">Siswa</option>
                                    <option value="guru">Guru</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pilih Kelas (Jika Siswa)</label>
                                <select class="form-select" id="kelasFilter" name="id_kelas">
                                    <option value="">-- Semua Kelas --</option>
                                    <?php while($k = $resultKelas->fetch_assoc()): ?>
                                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pilih Nama</label>
                                <select class="form-select" id="userId" name="user_id" required>
                                    <option value="">-- Pilih User --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">ID Di Sensor (0-162)</label>
                                <input type="number" class="form-control" name="fingerprint_id" min="0" max="162" required placeholder="Contoh: 0">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i></button>
                            </div>
                        </form>
                        <div class="mt-2 text-muted small">
                            * <strong>ID Di Sensor</strong> diisi berdasarkan nomor slot yang diminta oleh ESP32 saat proses enroll fisik di sensor.
                        </div>
                    </div>
                </div>

                <!-- Tabel Data Sidik Jari -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Sidik Jari Terdaftar (<?= $resultSidik->num_rows ?> / 162)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Slot ID</th>
                                        <th>Tipe</th>
                                        <th>NIS/NIP</th>
                                        <th>Nama</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($resultSidik->num_rows > 0): ?>
                                        <?php while ($sj = $resultSidik->fetch_assoc()): ?>
                                            <tr>
                                                <td><span class="badge bg-dark"># <?= $sj['fingerprint_id'] ?></span></td>
                                                <td><?= ucfirst($sj['tipe_user']) ?></td>
                                                <td><?= htmlspecialchars($sj['nomor_induk']) ?></td>
                                                <td><strong><?= htmlspecialchars($sj['nama_user']) ?></strong></td>
                                                <td><?= htmlspecialchars($sj['keterangan']) ?></td>
                                                <td>
                                                    <a href="<?= BASE_URL ?>actions/admin/sidik-jari-hapus.php?id=<?= $sj['id'] ?>" 
                                                       class="btn btn-danger btn-sm" onclick="return confirm('Hapus pemetaan sidik jari ini?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada sidik jari terdaftar.</td></tr>
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

    <!-- Script untuk Dynamic Dropdown (Relasi: Tipe -> Kelas -> Nama) -->
    <script>
        const tipeEl = document.getElementById('tipeUser');
        const kelasEl = document.getElementById('kelasFilter');
        const userEl = document.getElementById('userId');

        function loadUsers() {
            const tipe = tipeEl.value;
            const kelas = kelasEl.value;
            
            // Reset dropdown user
            userEl.innerHTML = '<option value="">-- Pilih User --</option>';

            let url = `<?= BASE_URL ?>actions/admin/get-user-ajax.php?tipe=${tipe}&kelas=${kelas}`;
            
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    data.forEach(user => {
                        const opt = document.createElement('option');
                        opt.value = user.id;
                        opt.textContent = user.nama + ' (' + user.nomor_induk + ')';
                        userEl.appendChild(opt);
                    });
                });
        }

        tipeEl.addEventListener('change', function() {
            // Sembunyikan/Perlihatkan dropdown kelas
            kelasEl.parentElement.style.display = this.value === 'siswa' ? 'block' : 'none';
            if(this.value === 'guru') kelasEl.value = '';
            loadUsers();
        });

        kelasEl.addEventListener('change', loadUsers);

        // Trigger awal
        kelasEl.parentElement.style.display = 'none';
        loadUsers();
    </script>
</body>
</html>