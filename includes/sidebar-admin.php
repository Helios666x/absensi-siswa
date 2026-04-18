<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-fingerprint me-2"></i> Admin Panel
    </div>
    <nav class="nav flex-column mt-2">
        <a href="<?= BASE_URL ?>dashboards/admin.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'admin.php') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <div class="nav-link text-uppercase small fw-bold mt-3 mb-1 text-secondary">Master Data</div>
        <a href="<?= BASE_URL ?>admin/master-kelas.php" class="nav-link">
            <i class="fas fa-school"></i> Kelas & Kategori
        </a>
        <a href="<?= BASE_URL ?>admin/master-siswa.php" class="nav-link">
            <i class="fas fa-user-graduate"></i> Data Siswa
        </a>
        <a href="<?= BASE_URL ?>admin/master-guru.php" class="nav-link">
            <i class="fas fa-chalkboard-teacher"></i> Data Guru
        </a>
        <a href="<?= BASE_URL ?>admin/master-jadwal.php" class="nav-link">
            <i class="fas fa-calendar-alt"></i> Jadwal Pelajaran
        </a>

        <div class="nav-link text-uppercase small fw-bold mt-3 mb-1 text-secondary">Perangkat</div>
        <a href="<?= BASE_URL ?>admin/master-perangkat.php" class="nav-link">
            <i class="fas fa-microchip"></i> Device ESP32
        </a>
        <a href="<?= BASE_URL ?>admin/master-sidik-jari.php" class="nav-link">
            <i class="fas fa-fingerprint"></i> Sidik Jari
        </a>

        <div class="nav-link text-uppercase small fw-bold mt-3 mb-1 text-secondary">Laporan</div>
        <a href="<?= BASE_URL ?>admin/laporan-global.php" class="nav-link">
            <i class="fas fa-file-pdf"></i> Cetak Laporan
        </a>
        <a href="<?= BASE_URL ?>admin/pengaturan-jam.php" class="nav-link">
            <i class="fas fa-clock"></i> Pengaturan Jam
        </a>
    </nav>
</div>