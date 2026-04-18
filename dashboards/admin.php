<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';

// Pengecekan paling sederhana, tanpa fungsi helper
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

 $page_title = "Dashboard Admin";
require_once __DIR__ . '/../includes/head.php';
?>
<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/../includes/sidebar-admin.php'; ?>
        <div class="main-content">
            <?php require_once __DIR__ . '/../includes/topbar.php'; ?>
            <div class="content">
                <h4>Selamat Datang di Dashboard Admin!</h4>
                <p class="text-muted">Jika kamu melihat tulisan ini, berarti loop redirect sudah BERHASIL dihentikan.</p>
                
                <div class="alert alert-success">
                    <strong>Berhasil!</strong> Layout sidebar dan topbar berjalan sempurna.
                </div>
            </div>
            <?php require_once __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>
</body>
</html>