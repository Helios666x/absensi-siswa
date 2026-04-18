<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-user-graduate me-2"></i> Panel Siswa
    </div>
    <nav class="nav flex-column mt-2">
        <a href="<?= BASE_URL ?>dashboards/siswa.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'siswa.php') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <div class="nav-link text-uppercase small fw-bold mt-3 mb-1 text-secondary">Kehadiran</div>
        <a href="<?= BASE_URL ?>siswa/riwayat-absensi.php" class="nav-link">
            <i class="fas fa-history"></i> Riwayat Absensi
        </a>
        <a href="<?= BASE_URL ?>siswa/izin-sakit.php" class="nav-link">
            <i class="fas fa-envelope"></i> Ajukan Izin/Sakit
        </a>
    </nav>
</div>