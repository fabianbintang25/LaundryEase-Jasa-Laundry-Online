<?php
session_start();
require 'includes/fungsi.php';

// jika sudah login, langsung ke dashboard
if (isset($_SESSION['nama'])) {
    header('Location: dashboard.php');
    exit;
}

$notif = '';
$tipe = 'error';

// pesan dari halaman lain (dikirim lewat URL)
if (isset($_GET['daftar'])) {
    $notif = 'Pendaftaran berhasil! Silakan masuk.';
    $tipe = 'sukses';
}
if (isset($_GET['keluar'])) {
    $notif = 'Kamu telah keluar dari akun.';
    $tipe = 'info';
}
if (($_GET['pesan'] ?? '') === 'harus-login') {
    $notif = 'Silakan masuk terlebih dahulu.';
    $tipe = 'info';
}

// COOKIE: isi otomatis kolom email jika sebelumnya memilih "Ingat saya"
$emailIsi = $_COOKIE['ingat_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $sandi = $_POST['sandi'] ?? '';
    $emailIsi = $email;

    $pengguna = cariPengguna($email);

    // password_verify() membandingkan sandi yang diketik dengan hash yang tersimpan
    if ($pengguna !== null && password_verify($sandi, $pengguna['sandi'])) {
        session_regenerate_id(true); // ganti ID session demi keamanan

        // SESSION: data pengguna disimpan di server selama sesi berlangsung
        $_SESSION['nama']  = $pengguna['nama'];
        $_SESSION['email'] = $pengguna['email'];

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

    $notif = 'Email atau kata sandi salah!';
    $tipe = 'error';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk — LaundryEase</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-brand">
    <a href="index.php" class="brand">
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3c2 3 4 6 4 9a4 4 0 1 1-8 0c0-3 2-6 4-9Z" fill="#1F5F63"/></svg></span>
      LaundryEase
    </a>
    <h2>Selamat datang kembali.</h2>
    <p>Masuk untuk lihat status cucian, pesanan aktif, dan sewa peralatan.</p>
    <div class="deco c1"></div>
    <div class="deco c2"></div>
  </div>

  <div class="auth-form-side">
    <div class="auth-card">
      <h1>Masuk ke akun kamu</h1>
      <p class="subtitle">Kelola pesanan laundry dan sewa dalam satu tempat</p>

     <form method="post" action="login.php">
  <div class="field">
    <label for="loginEmail">Email atau Username</label>
    <input type="text" id="loginEmail" name="email" value="<?= htmlspecialchars($emailIsi) ?>" placeholder="nama@email.com" required>
  </div>
  <div class="field">
    <label for="loginPassword">Kata Sandi</label>
    <div class="input-wrap">
      <input type="password" id="loginPassword" name="sandi" placeholder="••••••••" required>
      <button type="button" class="toggle-pw" data-target="loginPassword">Lihat</button>
    </div>
  </div>
  <div class="form-meta">
    <label class="remember"><input type="checkbox" name="ingat" <?= $emailIsi !== '' ? 'checked' : '' ?>> Ingat saya</label>
    <a href="#">Lupa kata sandi?</a>
  </div>
  <button type="submit" class="btn btn-primary btn-block">Masuk</button>
</form>

      <div class="auth-foot">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
      </div>
    </div>
  </div>
</div>

<script src="js/main.js"></script>
<?php if ($notif !== ''): ?>
<script>showToast(<?= json_encode($notif) ?>, '<?= $tipe ?>', 4000);</script>
<?php endif; ?>
</script>
</body>
</html>
