<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php?pesan=harus-login');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$idPesanan  = (int) ($_POST['id_pesanan'] ?? 0);
$idPengguna = (int) $_SESSION['id_pengguna'];

// DELETE: hanya pesanan milik pengguna yang sedang login yang dapat dihapus
$stmt = mysqli_prepare($koneksi, 'DELETE FROM pesanan WHERE id_pesanan = ? AND id_pengguna = ?');
mysqli_stmt_bind_param($stmt, 'ii', $idPesanan, $idPengguna);
mysqli_stmt_execute($stmt);

header('Location: dashboard.php?pesan=' . (mysqli_stmt_affected_rows($stmt) > 0 ? 'hapus' : 'gagal'));
exit;
