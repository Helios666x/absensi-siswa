<div class="topbar">
    <div class="d-flex align-items-center">
        <!-- Tombol Hamburger untuk Mobile -->
        <button class="btn btn-sidebar-toggle btn-sm me-3" onclick="toggleSidebar()">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <h5 class="mb-0 text-muted"><?= $page_title ?? 'Dashboard' ?></h5>
    </div>
    
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle me-1"></i> 
            <?= ucfirst($_SESSION['role']) ?>: <strong><?= $_SESSION['username'] ?></strong>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= BASE_URL . strtolower($_SESSION['role']) . '/pengaturan-akun.php' ?>"><i class="fas fa-cog me-2"></i>Pengaturan Akun</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>actions/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
    </div>
</div>