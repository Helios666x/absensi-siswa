<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/helper.php';

check_role([ROLE_GURU]);

 $page_title = "Absensi Kelas (Sesi)";
require_once __DIR__ . '/../includes/head.php';

 $guru_id = $_SESSION['related_id'];

// Konversi hari ini ke Bahasa Indonesia
 $hariInggris = date('l');
 $hariMap = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
 $hariIni = $hariMap[$hariInggris] ?? '';

// Ambil jadwal guru HARI INI
 $queryJadwal = "SELECT j.*, k.nama_kelas 
                FROM jadwal j 
                JOIN kelas k ON j.kelas_id = k.id 
                WHERE j.guru_id = $guru_id AND j.hari = '$hariIni' 
                ORDER BY j.jam_mulai ASC";
 $resultJadwal = $mysqli->query($queryJadwal);

// Ambil daftar device ESP32 yang tersedia
 $resultDevice = $mysqli->query("SELECT id, nama_device, lokasi FROM device ORDER BY lokasi ASC");

 $success = get_flashdata('success');
 $error = get_flashdata('error');
?>
<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/../includes/sidebar-guru.php'; ?>
        
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

                <div class="alert alert-info">
                    <i class="fas fa-calendar-day me-2"></i> <strong>Jadwal Hari Ini (<?= $hariIni ?>):</strong> 
                    Anda memiliki <?= $resultJadwal->num_rows ?> jadwal mengajar.
                </div>

                <!-- List Jadwal Hari Ini -->
                <div class="row mb-4">
                    <?php if ($resultJadwal->num_rows > 0): ?>
                        <?php while ($jadwal = $resultJadwal->fetch_assoc()): 
                            // Cek apakah jadwal ini sedang berjalan di salah satu device
                            $cekSesi = $mysqli->query("SELECT d.id AS device_id, d.nama_device FROM device d WHERE d.sesi_jadwal_id = " . $jadwal['id']);
                            $sesiAktif = $cekSesi->fetch_assoc();
                        ?>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100 <?= $sesiAktif ? 'border-start border-4 border-success' : '' ?>">
                                    <div class="card-body">
                                        <h6 class="fw-bold"><?= htmlspecialchars($jadwal['nama_kegiatan']) ?></h6>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-school me-1"></i> <?= htmlspecialchars($jadwal['nama_kelas']) ?>
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-clock me-1"></i> <?= substr($jadwal['jam_mulai'], 0, 5) ?> - <?= substr($jadwal['jam_selesai'], 0, 5) ?>
                                        </p>
                                        
                                        <?php if ($sesiAktif): ?>
                                            <span class="badge bg-success mb-2"><i class="fas fa-signal me-1"></i>SESII AKTIF (<?= htmlspecialchars($sesiAktif['nama_device']) ?>)</span>
                                            <br>
                                            <button type="button" class="btn btn-danger btn-sm btn-stop" data-device="<?= $sesiAktif['device_id'] ?>">
                                                <i class="fas fa-stop me-1"></i> Akhiri Sesi
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm btn-monitor" data-jadwal="<?= $jadwal['id'] ?>" data-kelas="<?= htmlspecialchars($jadwal['nama_kelas']) ?>" data-kegiatan="<?= htmlspecialchars($jadwal['nama_kegiatan']) ?>">
                                                <i class="fas fa-eye me-1"></i> Buka Monitor
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline-primary btn-sm w-100 btn-start" 
                                                    data-bs-toggle="modal" data-bs-target="#modalMulai"
                                                    data-jadwal="<?= $jadwal['id'] ?>"
                                                    data-info="<?= htmlspecialchars($jadwal['nama_kegiatan'] . ' - ' . $jadwal['nama_kelas']) ?>">
                                                <i class="fas fa-play me-1"></i> Mulai Sesi
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center text-muted py-4">Tidak ada jadwal mengajar hari ini.</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Area Monitor Real-Time (Awalnya Tersembunyi) -->
                <div id="monitorArea" style="display: none;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-fingerprint text-success me-2"></i>Monitor: <span id="monKelas"></span> (<span id="monKegiatan"></span>)</h5>
                            <button class="btn btn-secondary btn-sm" onclick="closeMonitor()">Tutup Monitor</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Waktu</th>
                                            <th>Metode</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyRealtime">
                                        <tr><td colspan="5" class="text-center text-muted py-3">Menunggu siswa menempelkan jari...</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <hr>
                            <!-- Form Input Manual untuk yang tidak hadir / sakit -->
                            <h6 class="mt-3">Input Manual (Sakit/Izin/Tanpa Keterangan)</h6>
                            <form id="formManual" class="row g-2">
                                <input type="hidden" id="manualJadwalId">
                                <div class="col-md-4">
                                    <select class="form-select form-select-sm" id="manualSiswaId" required>
                                        <option value="">-- Pilih Siswa --</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm" id="manualStatus" required>
                                        <option value="izin">Izin</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="alpha">Alpha</option>
                                        <option value="hadir">Hadir (Manual)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-control-sm" id="manualKet" placeholder="Keterangan (Opsional)">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-warning w-100">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <?php require_once __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>

    <!-- Modal Pilih Device -->
    <div class="modal fade" id="modalMulai" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formMulaiSesi">
                    <div class="modal-header">
                        <h5 class="modal-title">Mulai Sesi Absensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Anda akan memulai sesi: <strong id="infoJadwal"></strong></p>
                        <input type="hidden" id="mulaiJadwalId">
                        
                        <label class="form-label">Pilih Device ESP32 di Ruangan Anda:</label>
                        <select class="form-select" id="mulaiDeviceId" required>
                            <option value="">-- Pilih ESP32 --</option>
                            <?php while ($dev = $resultDevice->fetch_assoc()): ?>
                                <option value="<?= $dev['id'] ?>"><?= htmlspecialchars($dev['nama_device']) ?> (<?= htmlspecialchars($dev['lokasi']) ?>)</option>
                            <?php endwhile; ?>
                        </select>
                        <div class="form-text">Pastikan ESP32 yang dipilih benar-benar ada di ruangan kelas Anda.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-play me-1"></i>Mulai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let activeJadwalId = null;
        let pollingInterval = null;

        // 1. Isi Modal saat tombol "Mulai Sesi" diklik
        document.querySelectorAll('.btn-start').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('mulaiJadwalId').value = this.dataset.jadwal;
                document.getElementById('infoJadwal').innerText = this.dataset.info;
            });
        });

        // 2. Proses Mulai Sesi via AJAX
        document.getElementById('formMulaiSesi').addEventListener('submit', function(e) {
            e.preventDefault();
            const jadwalId = document.getElementById('mulaiJadwalId').value;
            const deviceId = document.getElementById('mulaiDeviceId').value;

            fetch('<?= BASE_URL ?>actions/guru/mulai-sesi.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `jadwal_id=${jadwalId}&device_id=${deviceId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Sesi berhasil dimulai! ESP32 siap menerima tap jari.');
                    location.reload(); // Reload untuk update UI tombol
                } else {
                    alert('Gagal: ' + data.message);
                }
            });
        });

        // 3. Proses Stop Sesi
        document.querySelectorAll('.btn-stop').forEach(btn => {
            btn.addEventListener('click', function() {
                if(confirm('Akhiri sesi absensi ini?')) {
                    const deviceId = this.dataset.device;
                    fetch('<?= BASE_URL ?>actions/guru/stop-sesi.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `device_id=${deviceId}`
                    }).then(() => location.reload());
                }
            });
        });

        // 4. Buka Monitor Real-time
        document.querySelectorAll('.btn-monitor').forEach(btn => {
            btn.addEventListener('click', function() {
                activeJadwalId = this.dataset.jadwal;
                document.getElementById('monKelas').innerText = this.dataset.kelas;
                document.getElementById('monKegiatan').innerText = this.dataset.kegiatan;
                document.getElementById('manualJadwalId').value = activeJadwalId;
                
                document.getElementById('monitorArea').style.display = 'block';
                loadSiswaUntukManual(activeJadwalId); // Load dropdown siswa yang belum absen
                startPolling();
            });
        });

        function closeMonitor() {
            document.getElementById('monitorArea').style.display = 'none';
            stopPolling();
        }

        // 5. Polling (Cek data baru setiap 3 detik)
        function startPolling() {
            updateRealtimeTable(); // Langsung panggil sekali
            pollingInterval = setInterval(updateRealtimeTable, 3000);
        }

        function stopPolling() {
            clearInterval(pollingInterval);
        }

        function updateRealtimeTable() {
            fetch(`<?= BASE_URL ?>actions/guru/get-presensi-realtime.php?jadwal_id=${activeJadwalId}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('tbodyRealtime');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Menunggu siswa menempelkan jari...</td></tr>';
                    return;
                }
                let html = '';
                data.forEach((row, index) => {
                    html += `<tr class="animate fadeIn">
                        <td>${index + 1}</td>
                        <td>${row.nis}</td>
                        <td><strong>${row.nama}</strong></td>
                        <td>${row.waktu}</td>
                        <td><span class="badge bg-${row.metode === 'fingerprint' ? 'success' : 'warning'}">${row.metode}</span></td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            });
        }

        // 6. Load Dropdown Siswa (yang belum tercatat di sesi ini)
        function loadSiswaUntukManual(jadwalId) {
            fetch(`<?= BASE_URL ?>actions/guru/get-siswa-belum-absen.php?jadwal_id=${jadwalId}`)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('manualSiswaId');
                select.innerHTML = '<option value="">-- Pilih Siswa --</option>';
                data.forEach(row => {
                    select.innerHTML += `<option value="${row.id}">${row.nis} - ${row.nama}</option>`;
                });
            });
        }

        // 7. Simpan Absensi Manual
        document.getElementById('formManual').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('<?= BASE_URL ?>actions/guru/absensi-manual-simpan.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Absensi manual berhasil disimpan.');
                    updateRealtimeTable(); // Refresh tabel
                    loadSiswaUntukManual(activeJadwalId); // Refresh dropdown
                    document.getElementById('manualKet').value = '';
                } else {
                    alert('Gagal: ' + data.message);
                }
            });
        });
    </script>
</body>
</html>