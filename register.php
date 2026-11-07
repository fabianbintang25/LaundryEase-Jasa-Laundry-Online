<?php
session_start();
require 'includes/fungsi.php';

// jika sudah login, tidak perlu mendaftar lagi
if (isset($_SESSION['nama'])) {
    header('Location: dashboard.php');
    exit;
}

$notif = '';
$nama = $email = $telepon = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // trim() membuang spasi di awal dan akhir input
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $sandi   = $_POST['sandi'] ?? '';

    if ($nama === '' || $email === '' || $telepon === '' || $sandi === '') {
        $notif = 'Semua kolom wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $notif = 'Format email tidak valid!';
    } elseif (strlen($sandi) < 8) {
        $notif = 'Kata sandi minimal 8 karakter!';
    } elseif (cariPengguna($email) !== null) {
        $notif = 'Email sudah terdaftar, silakan masuk.';
    } else {
        $daftar = ambilPengguna();
        $daftar[] = [
            'nama'    => $nama,
            'email'   => $email,
            'telepon' => $telepon,
            'sandi'   => password_hash($sandi, PASSWORD_DEFAULT), // sandi disimpan dalam bentuk hash
        ];
        simpanPengguna($daftar);
        header('Location: login.php?daftar=sukses');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar — LaundryEase</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrap register">
  <div class="auth-form-side">
    <div class="auth-card">
      <h1>Buat akun baru</h1>
      <p class="subtitle">Lengkapi data diri untuk mulai memesan</p>

     <form method="post" action="register.php">
  <div class="field">
    <label for="regName">Nama Lengkap</label>
    <input type="text" id="regName" name="nama" value="<?= htmlspecialchars($nama) ?>" placeholder="Nama sesuai KTP" required>
  </div>
  <div class="field">
    <label for="regEmail">Email</label>
    <input type="email" id="regEmail" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="nama@email.com" required>
  </div>
  <div class="field">
    <label for="regPhone">Nomor Telepon</label>
    <input type="tel" id="regPhone" name="telepon" value="<?= htmlspecialchars($telepon) ?>" placeholder="08xx-xxxx-xxxx" required>
  </div>
  <div class="field">
    <label for="regPassword">Kata Sandi</label>
    <div class="input-wrap">
      <input type="password" id="regPassword" name="sandi" placeholder="Minimal 8 karakter" minlength="8" required>
      <button type="button" class="toggle-pw" data-target="regPassword">Lihat</button>
    </div>
  </div>
  <button type="submit" class="btn btn-accent btn-block">Daftar</button>
</form>

      <div class="auth-foot">
        Sudah punya akun? <a href="login.php">Masuk di sini</a>
      </div>
    </div>
  </div>

  <div class="auth-brand">
    <a href="index.php" class="brand">
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3c2 3 4 6 4 9a4 4 0 1 1-8 0c0-3 2-6 4-9Z" fill="#1F5F63"/></svg></span>
      LaundryEase
    </a>
    <h2>Gabung dan mulai laundry lebih mudah.</h2>
    <p>Daftar sekali, pesan jemput-antar dan sewa alat kapan saja.</p>
    <div class="deco c1"></div>
    <div class="deco c2"></div>
  </div>
</div>

<script src="js/main.js"></script>
<script>
document.getElementById('registerForm').addEventListener('submit', function(e){
  e.preventDefault();
  // Simulasi pendaftaran sederhana (tanpa backend)
  window.location.href = 'login.html';
});
</script>
</body>
</html>
