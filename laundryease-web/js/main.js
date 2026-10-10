// ===== Navbar mobile toggle (index.html) =====
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');
if (menuToggle && navLinks) {
  menuToggle.addEventListener('click', () => navLinks.classList.toggle('open'));
}

// ===== Sidebar mobile toggle (dashboard.html) =====
const hamburger = document.getElementById('hamburger');
const sidebar = document.getElementById('sidebar');
if (hamburger && sidebar) {
  hamburger.addEventListener('click', () => sidebar.classList.toggle('open'));
}

// ===== Show / hide password (login.html, register.html) =====
document.querySelectorAll('.toggle-pw').forEach(btn => {
  btn.addEventListener('click', () => {
    const input = document.getElementById(btn.dataset.target);
    if (!input) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.textContent = isHidden ? 'Sembunyikan' : 'Lihat';
  });
});

// ===== Toast / Snackbar (dipakai semua halaman, tanpa framework) =====
function showToast(pesan, tipe = 'info', durasi = 3000) {
  let kotak = document.getElementById('toastBox');
  if (!kotak) {                       // buat wadah otomatis bila halaman belum punya
    kotak = document.createElement('div');
    kotak.id = 'toastBox';
    kotak.className = 'toast-box';
    kotak.setAttribute('aria-live', 'polite');
    document.body.append(kotak);
  }

  const toast = document.createElement('div');
  toast.className = 'toast ' + tipe;
  toast.setAttribute('role', 'status');

  const teks = document.createElement('span');
  teks.textContent = pesan;

  const tutup = document.createElement('button');
  tutup.type = 'button';
  tutup.setAttribute('aria-label', 'Tutup notifikasi');
  tutup.textContent = '\u00D7';

  toast.append(teks, tutup);
  kotak.append(toast);

  // jeda 1 frame supaya animasi muncul berjalan
  requestAnimationFrame(() => toast.classList.add('tampil'));

  const sembunyikan = () => {
    toast.classList.remove('tampil');
    setTimeout(() => toast.remove(), 300);
  };
  tutup.addEventListener('click', sembunyikan);
  setTimeout(sembunyikan, durasi);
}
