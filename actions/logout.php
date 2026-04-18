<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../config/constants.php';

session_unset();
session_destroy();

set_flashdata('success', 'Anda telah berhasil logout.');
redirect(BASE_URL . 'index.php');