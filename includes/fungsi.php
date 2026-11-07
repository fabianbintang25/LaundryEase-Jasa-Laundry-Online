<?php
// Lokasi file penyimpanan data pengguna (pengganti database sederhana)
define('FILE_PENGGUNA', __DIR__ . '/../data/pengguna.json');

// Membaca semua pengguna dari file JSON, hasilnya berupa array
function ambilPengguna() {
    if (!file_exists(FILE_PENGGUNA)) {
        return [];
    }
    $data = json_decode(file_get_contents(FILE_PENGGUNA), true);
    return is_array($data) ? $data : [];
}

// Menyimpan array pengguna ke file JSON
function simpanPengguna($daftar) {
    $folder = dirname(FILE_PENGGUNA);
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true); // buat folder data jika belum ada
    }
    file_put_contents(FILE_PENGGUNA, json_encode($daftar, JSON_PRETTY_PRINT), LOCK_EX);
}

// Mencari pengguna berdasarkan email (huruf besar/kecil diabaikan)
function cariPengguna($email) {
    foreach (ambilPengguna() as $p) {
        if (strtolower($p['email']) === strtolower($email)) {
            return $p;
        }
    }
    return null;
}