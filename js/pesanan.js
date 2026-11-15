/* =========================================================
   LaundryEase Dashboard - modal Tambah dan Edit Pesanan
   Data dikirim ke pesanan-proses.php (PHP + MySQL).
   Fungsi openModal() berasal dari dashboard.js.
   ========================================================= */

// Tombol "+ Tambah Pesanan": kosongkan form, isi tanggal hari ini, lalu buka modal
document.getElementById('btnAddPesanan').addEventListener('click', function () {
  document.getElementById('pesananForm').reset();
  document.getElementById('pesananId').value = '';
  document.getElementById('pesananTanggal').value = new Date().toISOString().slice(0, 10);
  document.getElementById('pesananModalTitle').textContent = 'Tambah Pesanan';
  openModal('pesananModal');
});

// Tombol "Edit": isi form memakai atribut data-* yang dicetak oleh PHP pada tombol
document.querySelectorAll('.btn-edit-pesanan').forEach(function (tombol) {
  tombol.addEventListener('click', function () {
    document.getElementById('pesananId').value = tombol.dataset.id;
    document.getElementById('pesananLayanan').value = tombol.dataset.layanan;
    document.getElementById('pesananJumlah').value = tombol.dataset.jumlah;
    document.getElementById('pesananTanggal').value = tombol.dataset.tanggal;
    document.getElementById('pesananStatus').value = tombol.dataset.status;
    document.getElementById('pesananModalTitle').textContent = 'Edit Pesanan';
    openModal('pesananModal');
  });
});
