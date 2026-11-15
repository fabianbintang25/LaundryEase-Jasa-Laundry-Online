<?php
session_start();

// jika sudah login, tidak perlu mendaftar lagi
if (isset($_SESSION['nama'])) {
    header('Location: dashboard.php');
    exit;
}

// pesan kesalahan dari register-proses.php (dikirim lewat URL)
$daftarError = [
    'kosong' => 'Semua kolom wajib diisi!',
    'email'  => 'Format email tidak valid!',
    'pendek' => 'Kata sandi minimal 8 karakter!',
    'dobel'  => 'Email sudah terdaftar, silakan masuk.',
    'gagal'  => 'Terjadi kesalahan, silakan coba lagi.',
];
$notif = $daftarError[$_GET['error'] ?? ''] ?? '';

// isian sebelumnya agar tidak perlu mengetik ulang
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$nama    = $old['nama'] ?? '';
$email   = $old['email'] ?? '';
$telepon = $old['telepon'] ?? '';
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

      <form action="register-proses.php" method="post">
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
<?php if ($notif !== ''): ?>
<script>showToast(<?= json_encode($notif) ?>, 'error', 5000);</script>
<?php endif; ?>
</body>
</html>
