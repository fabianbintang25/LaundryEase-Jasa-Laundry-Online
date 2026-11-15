<?php
// Pengaturan koneksi ke database MySQL (Laragon: user root, password kosong)
$servername = 'localhost';
$username   = 'root';
$password   = ''; // di Laragon biasanya kosong, isi jika Anda memberi password
$database   = 'laundryease_db';

// membuat koneksi
$koneksi = mysqli_connect($servername, $username, $password, $database);

// mengecek koneksi
if (!$koneksi) {
    die('Connection Failed: ' . mysqli_connect_error());
}

// agar huruf Indonesia dan simbol tampil dengan benar
mysqli_set_charset($koneksi, 'utf8mb4');
