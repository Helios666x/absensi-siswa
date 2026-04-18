<?php
function set_flashdata($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function get_flashdata($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}