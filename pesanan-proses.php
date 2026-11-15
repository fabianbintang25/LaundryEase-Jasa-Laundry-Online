<?php
session_start();
include 'koneksi.php';

// hanya pengguna yang sudah login yang boleh mengelola pesanan
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php?pesan=harus-login');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$idPengguna = (int) $_SESSION['id_pengguna'];
$idPesanan  = (int) ($_POST['id_pesanan'] ?? 0);      // 0 = pesanan baru, lebih dari 0 = ubah
$idLayanan  = (int) ($_POST['id_layanan'] ?? 0);
$jumlah     = (float) ($_POST['jumlah'] ?? 0);
$tanggal    = $_POST['tanggal_pesan'] ?? '';
$status     = $_POST['status'] ?? '';

// validasi isian
if ($idLayanan <= 0 || $jumlah <= 0 || $tanggal === '' || !in_array($status, ['tunggu', 'proses', 'selesai'])) {
    header('Location: dashboard.php?pesan=kosong');
    exit;
}

// ambil harga layanan untuk menghitung total bayar
$stmt = mysqli_prepare($koneksi, 'SELECT harga, satuan FROM layanan WHERE id_layanan = ?');
mysqli_stmt_bind_param($stmt, 'i', $idLayanan);
mysqli_stmt_execute($stmt);
$layanan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$layanan) {
    header('Location: dashboard.php?pesan=gagal');
    exit;
}

$total = $layanan['harga'] * $jumlah;
if ($layanan['satuan'] === 'kg' && $jumlah >= 5) {
    $total = $total * 0.9; // diskon 10% untuk cucian kiloan 5 kg atau lebih
}
$total = (int) round($total);

if ($idPesanan > 0) {
    // UPDATE: ubah pesanan milik pengguna yang sedang login
    $sql = 'UPDATE pesanan SET id_layanan = ?, tanggal_pesan = ?, jumlah = ?, total_bayar = ?, status = ?
            WHERE id_pesanan = ? AND id_pengguna = ?';
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, 'isdisii', $idLayanan, $tanggal, $jumlah, $total, $status, $idPesanan, $idPengguna);
    $hasil = 'ubah';
} else {
    // CREATE: tambah pesanan baru
    $sql = 'INSERT INTO pesanan (id_pengguna, id_layanan, tanggal_pesan, jumlah, total_bayar, status)
            VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, 'iisdis', $idPengguna, $idLayanan, $tanggal, $jumlah, $total, $status);
    $hasil = 'tambah';
}

header('Location: dashboard.php?pesan=' . (mysqli_stmt_execute($stmt) ? $hasil : 'gagal'));
exit;
