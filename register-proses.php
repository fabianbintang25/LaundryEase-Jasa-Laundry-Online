<?php
session_start();
include 'koneksi.php';

// halaman ini hanya menerima data dari form register
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// trim() membuang spasi di awal dan akhir input
$nama    = trim($_POST['nama'] ?? '');
$email   = trim($_POST['email'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$sandi   = $_POST['sandi'] ?? '';

// simpan isian sementara agar form tidak kosong lagi bila terjadi kesalahan
$_SESSION['old'] = ['nama' => $nama, 'email' => $email, 'telepon' => $telepon];

if ($nama === '' || $email === '' || $telepon === '' || $sandi === '') {
    header('Location: register.php?error=kosong');
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php?error=email');
    exit;
}
if (strlen($sandi) < 8) {
    header('Location: register.php?error=pendek');
    exit;
}

// cek apakah email sudah terdaftar (prepared statement mencegah SQL Injection)
$cek = mysqli_prepare($koneksi, 'SELECT id_pengguna FROM pengguna WHERE email = ?');
mysqli_stmt_bind_param($cek, 's', $email);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);

if (mysqli_stmt_num_rows($cek) > 0) {
    header('Location: register.php?error=dobel');
    exit;
}

// simpan pengguna baru (CREATE), sandi disimpan dalam bentuk hash
$hash = password_hash($sandi, PASSWORD_DEFAULT);
$simpan = mysqli_prepare($koneksi, 'INSERT INTO pengguna (nama, email, telepon, sandi) VALUES (?, ?, ?, ?)');
mysqli_stmt_bind_param($simpan, 'ssss', $nama, $email, $telepon, $hash);

if (mysqli_stmt_execute($simpan)) {
    unset($_SESSION['old']);
    header('Location: login.php?daftar=sukses');
} else {
    header('Location: register.php?error=gagal');
}
exit;
