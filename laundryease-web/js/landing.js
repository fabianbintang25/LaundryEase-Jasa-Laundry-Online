/* =========================================================
   LaundryEase — DOM untuk Landing Page (index.html)
   Memanfaatkan showToast() dari main.js
   ========================================================= */

// Data layanan (Array berisi Object)
const daftarLayanan = [
  { nama: 'Cuci Kiloan',    harga: 7000,  satuan: 'kg',      info: 'Cuci, kering, setrika. Estimasi 6 jam.' },
  { nama: 'Cuci Sepatu',    harga: 25000, satuan: 'pasang',  info: 'Deep clean, kembali putih dan wangi.' },
  { nama: 'Dry Cleaning',   harga: 20000, satuan: 'pcs',     info: 'Untuk jas, gaun, dan bahan sensitif.' },
  { nama: 'Sewa Vacuum Cleaner', harga: 25000, satuan: 'hari', info: 'Sewa harian, bisa diantar ke rumah.' }
];

function rupiah(angka) {
  return 'Rp' + Number(angka).toLocaleString('id-ID');
}

// ===== DOM 1: getElementById + textContent (sapaan & tahun) =====
function tampilkanSapaan() {
  const jam = new Date().getHours();
  let sapaan = 'Selamat malam';

  if (jam < 11) {
    sapaan = 'Selamat pagi';
  } else if (jam < 15) {
    sapaan = 'Selamat siang';
  } else if (jam < 18) {
    sapaan = 'Selamat sore';
  }

  document.getElementById('sapaan').textContent = sapaan + ', selamat datang di LaundryEase!';
  document.getElementById('tahun').textContent = new Date().getFullYear();
}

// ===== DOM 2: createElement + append (kartu harga & opsi select) =====
function renderHarga() {
  const wadah = document.getElementById('daftarHarga');
  const pilihan = document.getElementById('calcLayanan');

  daftarLayanan.forEach(function (item, index) {
    const kartu = document.createElement('div');
    kartu.className = 'card harga-card';

    const judul = document.createElement('h3');
    judul.textContent = item.nama;

    const harga = document.createElement('div');
    harga.className = 'harga';
    harga.textContent = rupiah(item.harga) + ' / ' + item.satuan;

    const info = document.createElement('p');
    info.textContent = item.info;

    const tombol = document.createElement('button');
    tombol.type = 'button';
    tombol.className = 'btn btn-outline btn-sm';
    tombol.textContent = 'Pilih layanan';
    tombol.addEventListener('click', function () {
      pilihLayanan(index);
    });

    kartu.append(judul, harga, info, tombol);
    wadah.append(kartu);

    const opsi = document.createElement('option');
    opsi.value = index;
    opsi.textContent = item.nama;
    pilihan.append(opsi);
  });
}

// ===== DOM 3: setAttribute / getAttribute (label & placeholder mengikuti satuan) =====
function perbaruiSatuan() {
  const nilai = document.getElementById('calcLayanan').value;
  const label = document.getElementById('labelJumlah');
  const jumlah = document.getElementById('calcJumlah');

  if (nilai === '') {
    label.textContent = 'Jumlah';
    jumlah.setAttribute('placeholder', 'Pilih layanan dulu');
  } else {
    const satuan = daftarLayanan[nilai].satuan;
    label.textContent = 'Jumlah (' + satuan + ')';
    jumlah.setAttribute('placeholder', 'Masukkan jumlah dalam ' + satuan);
  }
  console.log('placeholder sekarang:', jumlah.getAttribute('placeholder'));
}

// ===== DOM 4: querySelector + element.style (bayangan navbar saat scroll) =====
function efekNavbar() {
  const navbar = document.querySelector('.navbar');
  navbar.style.boxShadow = window.scrollY > 10 ? '0 4px 16px rgba(31, 95, 99, 0.15)' : 'none';
}

// ===== DOM 5: querySelectorAll + classList (menu aktif saat diklik) =====
function aktifkanMenu() {
  const menu = document.querySelectorAll('.nav-links a');

  menu.forEach(function (link) {
    link.addEventListener('click', function () {
      menu.forEach(function (m) {
        m.classList.remove('active');
      });
      link.classList.add('active');
    });
  });
}

function pilihLayanan(index) {
  document.getElementById('calcLayanan').value = index;
  perbaruiSatuan();
  document.querySelector('.calc').scrollIntoView({ behavior: 'smooth', block: 'center' });
  showToast('Layanan "' + daftarLayanan[index].nama + '" dipilih', 'info');
}

function hitungEstimasi() {
  const nama = document.getElementById('calcNama').value.trim();
  const nilai = document.getElementById('calcLayanan').value;
  const jumlah = Number(document.getElementById('calcJumlah').value);

  if (nama === '' || nilai === '' || jumlah <= 0) {
    showToast('Lengkapi nama, layanan, dan jumlah dengan benar!', 'error');
    return;
  }

  const item = daftarLayanan[nilai];
  let total = item.harga * jumlah;
  let diskon = 0;

  if (item.satuan === 'kg' && jumlah >= 5) {
    diskon = total * 0.1;
    total = total - diskon;
  }

  const hasil = document.getElementById('hasilHitung');
  hasil.hidden = false;
  hasil.innerHTML =
    '<b>Rincian Estimasi</b><br>' +
    'Pelanggan : ' + nama + '<br>' +
    'Layanan : ' + item.nama + '<br>' +
    'Jumlah : ' + jumlah + ' ' + item.satuan + '<br>' +
    'Diskon : ' + rupiah(diskon) + '<br>' +
    '<b>Total : ' + rupiah(total) + '</b>';

  showToast('Estimasi berhasil dihitung, ' + nama + '!', 'sukses');
}

function resetForm() {
  document.getElementById('calcNama').value = '';
  document.getElementById('calcLayanan').value = '';
  document.getElementById('calcJumlah').value = '';
  document.getElementById('hasilHitung').innerHTML = '';
  document.getElementById('hasilHitung').hidden = true;
  perbaruiSatuan();
  showToast('Form telah dikosongkan', 'info');
}

// ===== Jalankan saat halaman dimuat =====
tampilkanSapaan();
renderHarga();
aktifkanMenu();
document.getElementById('calcLayanan').addEventListener('change', perbaruiSatuan);
document.getElementById('btnHitung').addEventListener('click', hitungEstimasi);
document.getElementById('btnReset').addEventListener('click', resetForm);
window.addEventListener('scroll', efekNavbar);
