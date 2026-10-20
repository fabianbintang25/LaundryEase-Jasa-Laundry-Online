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
function tampilkanSapaan(nama = '') {
  const jam = new Date().getHours();
  let sapaan = 'Selamat malam';

  if (jam < 11) {
    sapaan = 'Selamat pagi';
  } else if (jam < 15) {
    sapaan = 'Selamat siang';
  } else if (jam < 18) {
    sapaan = 'Selamat sore';
  }

  // jika nama diisi (dari prompt), sapa dengan nama; jika tidak, sapaan umum
  const kalimat = nama === ''
    ? sapaan + ', selamat datang di LaundryEase!'
    : sapaan + ', ' + nama + '! Selamat datang di LaundryEase.';

  document.getElementById('sapaan').textContent = kalimat;
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

// ===== PopUp Box 2: confirm() (konfirmasi sebelum form dikosongkan) =====
function resetForm() {
  // confirm() mengembalikan true jika klik OK, dan false jika klik Cancel
  const yakin = confirm('Yakin ingin mengosongkan semua isian form?');

  if (!yakin) {
    showToast('Pengosongan form dibatalkan', 'info');
    return;
  }

  document.getElementById('calcNama').value = '';
  document.getElementById('calcLayanan').value = '';
  document.getElementById('calcJumlah').value = '';
  document.getElementById('hasilHitung').innerHTML = '';
  document.getElementById('hasilHitung').hidden = true;
  perbaruiSatuan();
  estimasiCepat();
  showToast('Form telah dikosongkan', 'info');
}

// ===== PopUp Box 1: prompt() + PopUp Box 3: alert() (ganti nama sapaan) =====
function gantiNama() {
  // prompt(pertanyaan, nilaiDefault) mengembalikan teks, atau null jika klik Cancel
  const nama = prompt('Silakan masukkan nama kamu:', 'Pelanggan');

  if (nama === null) {
    return; // pengguna menekan Cancel
  }

  if (nama.trim() === '') {
    alert('Nama tidak boleh kosong!'); // alert() hanya memberi informasi
    return;
  }

  tampilkanSapaan(nama.trim());
  showToast('Halo, ' + nama.trim() + '! Sapaan berhasil diganti', 'sukses');
}

// ===== Event 1 (mouseover & mouseout) dan Event 2 (dblclick) pada kartu harga =====
function eventKartuHarga() {
  const wadah = document.getElementById('daftarHarga');

  // mouseover: kartu menyala saat kursor berada di atasnya
  wadah.addEventListener('mouseover', function (e) {
    const kartu = e.target.closest('.harga-card');
    if (kartu) {
      kartu.classList.add('aktif');
    }
  });

  // mouseout: nyala dimatikan saat kursor benar-benar keluar dari kartu
  wadah.addEventListener('mouseout', function (e) {
    const kartu = e.target.closest('.harga-card');
    if (kartu && !kartu.contains(e.relatedTarget)) {
      kartu.classList.remove('aktif');
    }
  });

  // dblclick: klik dua kali = pilih layanan (jalan pintas)
  wadah.addEventListener('dblclick', function (e) {
    const kartu = e.target.closest('.harga-card');
    if (kartu) {
      const index = Array.from(wadah.children).indexOf(kartu);
      pilihLayanan(index);
    }
  });

  // petunjuk saat kursor berhenti di kartu
  wadah.querySelectorAll('.harga-card').forEach(function (kartu) {
    kartu.setAttribute('title', 'Klik dua kali untuk memilih layanan');
  });
}

// ===== Event 3 (keyup): estimasi biaya langsung muncul saat jumlah diketik =====
function estimasiCepat() {
  const nilai = document.getElementById('calcLayanan').value;
  const jumlah = Number(document.getElementById('calcJumlah').value);
  const info = document.getElementById('liveHarga');

  if (nilai === '' || jumlah <= 0) {
    info.textContent = '';
    return;
  }

  const item = daftarLayanan[nilai];
  let total = item.harga * jumlah;

  if (item.satuan === 'kg' && jumlah >= 5) {
    total = total * 0.9; // diskon 10%
  }

  info.textContent = 'Perkiraan sementara: ' + rupiah(total);
}

// ===== Jalankan saat halaman dimuat =====
tampilkanSapaan();
renderHarga();
aktifkanMenu();
document.getElementById('calcLayanan').addEventListener('change', perbaruiSatuan);
document.getElementById('btnHitung').addEventListener('click', hitungEstimasi);
document.getElementById('btnReset').addEventListener('click', resetForm);
window.addEventListener('scroll', efekNavbar);

document.getElementById('btnGantiNama').addEventListener('click', gantiNama);
eventKartuHarga();
document.getElementById('calcJumlah').addEventListener('keyup', estimasiCepat);
document.getElementById('calcLayanan').addEventListener('change', estimasiCepat);
