<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth-check.php';
check_role([ROLE_GURU]);

 $page_title = "Dashboard Guru";
require_once __DIR__ . '/../includes/head.php';
?>
<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/../includes/sidebar-guru.php'; ?>
        
        <div class="main-content">
            <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
            
            <div class="content">
                <div class="mb-4">
                    <h4>Selamat Datang di Dashboard Guru</h4>
                    <p class="text-muted">Kelola jadwal dan proses absensi kelas Anda.</p>
                </div>
                
                <!-- Konten Dashboard Guru -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Silakan pilih menu "Absensi Kelas (Sesi)" di sidebar kiri untuk memulai sesi absensi.
                </div>
            </div>
            
            <?php require_once __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>
</body>
</html>