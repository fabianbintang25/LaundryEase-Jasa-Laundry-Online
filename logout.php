<?php
session_start();

$_SESSION = []; // kosongkan semua data session

// hapus cookie PHPSESSID di browser
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy(); // hancurkan session di server

header('Location: login.php?keluar=1');
exit;