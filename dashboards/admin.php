<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth-check.php';
check_role([ROLE_ADMIN]);

 $page_title = "Dashboard Admin";
require_once __DIR__ . '/../includes/head.php';
?>
<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/../includes/sidebar-admin.php'; ?>
        
        <div class="main-content">
            <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
            
            <div class="content">
                <div class="mb-4">
                    <h4>Selamat Datang di Dashboard Admin</h4>
                    <p class="text-muted">Kelola seluruh sistem absensi sekolah dari sini.</p>
                </div>

                <!-- Konten Dashboard Admin akan dibuat di step selanjutnya -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h6>Total Siswa</h6>
                                <h2 class="fw-bold">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h6>Total Guru</h6>
                                <h2 class="fw-bold">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h6>Device Online</h6>
                                <h2 class="fw-bold">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-danger text-white">
                            <div class="card-body">
                                <h6>Hadir Hari Ini</h6>
                                <h2 class="fw-bold">0</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php require_once __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>
</body>
</html>