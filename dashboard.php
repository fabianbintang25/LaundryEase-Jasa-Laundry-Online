<?php
session_start();
include 'koneksi.php';

// halaman ini hanya untuk pengguna yang sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php?pesan=harus-login');
    exit;
}

$nama = $_SESSION['nama'];
$idPengguna = (int) $_SESSION['id_pengguna'];

$labelStatus = ['proses' => 'Diproses', 'tunggu' => 'Menunggu Konfirmasi', 'selesai' => 'Selesai'];

// READ: daftar layanan untuk pilihan pada form
$layananList = mysqli_fetch_all(mysqli_query($koneksi, 'SELECT id_layanan, nama_layanan, satuan FROM layanan ORDER BY id_layanan'), MYSQLI_ASSOC);

// READ: pesanan milik pengguna yang sedang login
$sql = 'SELECT p.id_pesanan, p.id_layanan, l.nama_layanan, l.satuan, p.jumlah, p.tanggal_pesan, p.total_bayar, p.status
        FROM pesanan p
        JOIN layanan l ON l.id_layanan = p.id_layanan
        WHERE p.id_pengguna = ?
        ORDER BY p.tanggal_pesan DESC, p.id_pesanan DESC';
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, 'i', $idPengguna);
mysqli_stmt_execute($stmt);
$pesananList = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// angka ringkasan pada kartu statistik
$aktif = $selesai = $pengeluaran = 0;
foreach ($pesananList as $p) {
    if ($p['status'] !== 'selesai') {
        $aktif++;
    } else {
        $pengeluaran += $p['total_bayar'];
        if (date('Y-m', strtotime($p['tanggal_pesan'])) === date('Y-m')) {
            $selesai++;
        }
    }
}

// pesan hasil proses CRUD (dikirim lewat URL oleh pesanan-proses.php dan pesanan-hapus.php)
$pesanCrud = [
    'tambah' => ['Pesanan berhasil ditambahkan', 'sukses'],
    'ubah'   => ['Pesanan berhasil diubah', 'sukses'],
    'hapus'  => ['Pesanan berhasil dihapus', 'sukses'],
    'kosong' => ['Lengkapi semua data pesanan dengan benar!', 'error'],
    'gagal'  => ['Proses gagal, silakan coba lagi', 'error'],
];
$notif = $pesanCrud[$_GET['pesan'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard — LaundryEase</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="dash">

  <aside class="sidebar" id="sidebar">
    <a href="index.php" class="brand">
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3c2 3 4 6 4 9a4 4 0 1 1-8 0c0-3 2-6 4-9Z" fill="#1F5F63"/></svg></span>
      LaundryEase
    </a>
    <nav class="side-nav">
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="#pesanan-panel">Pesanan Saya</a>
      <a href="#" onclick="return false;">Riwayat Transaksi</a>
      <a href="#alat-panel">Sewa Peralatan</a>
      <a href="#" onclick="return false;">Profil Saya</a>
      <hr>
      <a href="logout.php">Keluar</a>
    </nav>
  </aside>

  <div class="dash-main">
    <header class="topbar">
      <button class="hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
      <h1>Dashboard</h1>
      <input class="search" type="text" placeholder="Cari pesanan...">
      <div class="user">
        <div class="avatar"></div>
        <div class="user-name"><strong><?= htmlspecialchars($nama) ?></strong><span>Pelanggan</span></div>
      </div>
    </header>

    <main class="dash-content">
      <section class="stat-grid">
        <div class="stat-card">
          <div class="label">Pesanan Aktif</div>
          <div class="value"><?= $aktif ?></div>
        </div>
        <div class="stat-card">
          <div class="label">Selesai Bulan Ini</div>
          <div class="value"><?= $selesai ?></div>
        </div>
        <div class="stat-card">
          <div class="label">Total Pengeluaran</div>
          <div class="value">Rp<?= number_format($pengeluaran, 0, ',', '.') ?></div>
        </div>
        <div class="stat-card accent">
          <div class="label">Alat Disewa</div>
          <div class="value" id="statAlat">0</div>
        </div>
      </section>

      <!-- ===== Data 1: Pesanan (CRUD) ===== -->
      <section class="panel" id="pesanan-panel">
        <div class="panel-head">
          <h2>Data Pesanan</h2>
          <button class="btn btn-primary btn-sm" id="btnAddPesanan">+ Tambah Pesanan</button>
        </div>
        <div class="table-wrap">
          <table class="data">
            <thead>
              <tr><th>Layanan</th><th>Jumlah</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
              <?php if (count($pesananList) === 0): ?>
                <tr class="empty-row"><td colspan="6">Belum ada data pesanan.</td></tr>
              <?php endif; ?>
              <?php foreach ($pesananList as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['nama_layanan']) ?></td>
                  <td><?= $p['jumlah'] + 0 ?> <?= $p['satuan'] ?></td>
                  <td><?= date('d M Y', strtotime($p['tanggal_pesan'])) ?></td>
                  <td>Rp<?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                  <td><span class="badge <?= $p['status'] ?>"><?= $labelStatus[$p['status']] ?></span></td>
                  <td class="row-actions">
                    <button type="button" class="btn-edit-pesanan"
                      data-id="<?= $p['id_pesanan'] ?>"
                      data-layanan="<?= $p['id_layanan'] ?>"
                      data-jumlah="<?= $p['jumlah'] + 0 ?>"
                      data-tanggal="<?= $p['tanggal_pesan'] ?>"
                      data-status="<?= $p['status'] ?>">Edit</button>
                    <form action="pesanan-hapus.php" method="post" onsubmit="return confirm('Hapus data pesanan ini?')">
                      <input type="hidden" name="id_pesanan" value="<?= $p['id_pesanan'] ?>">
                      <button type="submit" class="del">Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ===== Data 2: Peralatan Sewa (CRUD) ===== -->
      <section class="panel" id="alat-panel">
        <div class="panel-head">
          <h2>Data Peralatan Sewa</h2>
          <button class="btn btn-primary btn-sm" id="btnAddAlat">+ Tambah Alat</button>
        </div>
        <div class="table-wrap">
          <table class="data">
            <thead>
              <tr><th>Nama Alat</th><th>Stok</th><th>Harga / Hari</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody id="alatTableBody"></tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</div>

<!-- Modal: Pesanan -->
<div class="modal-backdrop" id="pesananModal">
  <div class="modal">
    <h3 id="pesananModalTitle">Tambah Pesanan</h3>
    <form id="pesananForm" action="pesanan-proses.php" method="post">
      <input type="hidden" id="pesananId" name="id_pesanan">
      <div class="field">
        <label for="pesananLayanan">Layanan</label>
        <select id="pesananLayanan" name="id_layanan" required>
          <?php foreach ($layananList as $l): ?>
            <option value="<?= $l['id_layanan'] ?>"><?= htmlspecialchars($l['nama_layanan']) ?> (per <?= $l['satuan'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="pesananJumlah">Jumlah</label>
        <input type="number" id="pesananJumlah" name="jumlah" min="0.5" step="0.5" value="1" required>
      </div>
      <div class="field">
        <label for="pesananTanggal">Tanggal</label>
        <input type="date" id="pesananTanggal" name="tanggal_pesan" required>
      </div>
      <div class="field">
        <label for="pesananStatus">Status</label>
        <select id="pesananStatus" name="status" required>
          <option value="proses">Diproses</option>
          <option value="tunggu">Menunggu Konfirmasi</option>
          <option value="selesai">Selesai</option>
        </select>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-outline" data-close="pesananModal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Peralatan -->
<div class="modal-backdrop" id="alatModal">
  <div class="modal">
    <h3 id="alatModalTitle">Tambah Alat</h3>
    <form id="alatForm">
      <input type="hidden" id="alatId">
      <div class="field">
        <label for="alatNama">Nama Alat</label>
        <input type="text" id="alatNama" placeholder="Contoh: Vacuum Cleaner" required>
      </div>
      <div class="field">
        <label for="alatStok">Stok</label>
        <input type="number" id="alatStok" min="0" value="1" required>
      </div>
      <div class="field">
        <label for="alatHarga">Harga Sewa / Hari (Rp)</label>
        <input type="number" id="alatHarga" min="0" step="1000" placeholder="25000" required>
      </div>
      <div class="field">
        <label for="alatStatus">Status</label>
        <select id="alatStatus" required>
          <option value="tersedia">Tersedia</option>
          <option value="disewa">Disewa</option>
        </select>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-outline" data-close="alatModal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="js/main.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/pesanan.js"></script>
<?php if ($notif): ?>
<script>showToast(<?= json_encode($notif[0]) ?>, '<?= $notif[1] ?>', 3500);</script>
<?php endif; ?>
</body>
</html>
