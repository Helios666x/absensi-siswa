<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/helper.php';

// Jika sudah login, lempar
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['role'])) {
    header("Location: " . BASE_URL . "dashboards/router.php");
    exit;
}

 $error = get_flashdata('error');
 $success = get_flashdata('success');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 400px; width: 100%; border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 40px; }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="text-center mb-4">
            <i class="fas fa-fingerprint fa-3x text-primary mb-3"></i>
            <h3>Sistem Absensi</h3>
            <p class="text-muted">Silakan login untuk melanjutkan</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>actions/login.php" method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">MASUK</button>
        </form>
    </div>
</body>
</html>