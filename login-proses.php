<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$sandi = $_POST['sandi'] ?? '';

// ambil data pengguna berdasarkan email (READ)
$stmt = mysqli_prepare($koneksi, 'SELECT id_pengguna, nama, email, sandi FROM pengguna WHERE email = ?');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$pengguna = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// password_verify() membandingkan sandi yang diketik dengan hash di database
if ($pengguna && password_verify($sandi, $pengguna['sandi'])) {
    session_regenerate_id(true); // ganti ID session demi keamanan

    // SESSION: data pengguna disimpan di server selama sesi berlangsung
    $_SESSION['id_pengguna'] = $pengguna['id_pengguna'];
    $_SESSION['nama']        = $pengguna['nama'];
    $_SESSION['email']       = $pengguna['email'];

    // COOKIE: disimpan di browser selama 7 hari jika "Ingat saya" dicentang
    $opsi = ['expires' => time() + 7 * 24 * 60 * 60, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax'];
    if (isset($_POST['ingat'])) {
        setcookie('ingat_email', $pengguna['email'], $opsi);
    } else {
        $opsi['expires'] = time() - 3600; // kedaluwarsa = cookie terhapus
        setcookie('ingat_email', '', $opsi);
    }

    header('Location: dashboard.php');
    exit;
}

// gagal: kembali ke halaman login, email yang diketik tetap terisi
$_SESSION['old_email'] = $email;
header('Location: login.php?error=salah');
exit;
