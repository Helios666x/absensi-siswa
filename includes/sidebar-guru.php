<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-chalkboard-teacher me-2"></i> Panel Guru
    </div>
    <nav class="nav flex-column mt-2">
        <a href="<?= BASE_URL ?>dashboards/guru.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'guru.php') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <div class="nav-link text-uppercase small fw-bold mt-3 mb-1 text-secondary">Absensi</div>
        <a href="<?= BASE_URL ?>guru/absensi-kelas.php" class="nav-link">
            <i class="fas fa-fingerprint"></i> Absensi Kelas (Sesi)
        </a>
        <a href="<?= BASE_URL ?>guru/absensi-manual.php" class="nav-link">
            <i class="fas fa-keyboard"></i> Input Manual
        </a>

        <div class="nav-link text-uppercase small fw-bold mt-3 mb-1 text-secondary">Laporan</div>
        <a href="<?= BASE_URL ?>guru/rekap-kelas.php" class="nav-link">
            <i class="fas fa-clipboard-list"></i> Rekap Kehadiran
        </a>
        <a href="<?= BASE_URL ?>guru/laporan-kelas.php" class="nav-link">
            <i class="fas fa-file-pdf"></i> Cetak PDF
        </a>
    </nav>
</div>