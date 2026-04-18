<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth-check.php';
check_role([ROLE_SISWA]);

 $page_title = "Dashboard Siswa";
require_once __DIR__ . '/../includes/head.php';
?>
<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/../includes/sidebar-siswa.php'; ?>
        
        <div class="main-content">
            <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
            
            <div class="content">
                <div class="mb-4">
                    <h4>Selamat Datang di Dashboard Siswa</h4>
                    <p class="text-muted">Pantau status kehadiran Anda setiap hari.</p>
                </div>
                
                <!-- Konten Dashboard Siswa -->
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i> Sistem berjalan dengan baik. Gunakan sidik jari Anda di gerbang untuk absensi pagi.
                </div>
            </div>
            
            <?php require_once __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>
</body>
</html>