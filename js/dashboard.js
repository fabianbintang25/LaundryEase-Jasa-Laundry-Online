/* =========================================================
   LaundryEase Dashboard — CRUD (localStorage, tanpa backend)
   Data 1: Pesanan
   Data 2: Peralatan Sewa
   ========================================================= */

const STATUS_LABEL = { proses: 'Diproses', tunggu: 'Menunggu Konfirmasi', selesai: 'Selesai' };
const STATUS_CLASS = { proses: 'proses', tunggu: 'tunggu', selesai: 'selesai' };
const ALAT_STATUS_LABEL = { tersedia: 'Tersedia', disewa: 'Disewa' };
const ALAT_STATUS_CLASS = { tersedia: 'selesai', disewa: 'proses' };

function loadData(key, seed) {
  const raw = localStorage.getItem(key);
  if (raw) { try { return JSON.parse(raw); } catch (e) { /* fall through */ } }
  localStorage.setItem(key, JSON.stringify(seed));
  return seed;
}
function saveData(key, data) { localStorage.setItem(key, JSON.stringify(data)); }
function uid() { return Date.now().toString(36) + Math.random().toString(36).slice(2, 7); }
function formatTanggal(iso) {
  if (!iso) return '-';
  const d = new Date(iso);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
function formatRupiah(n) { return 'Rp' + Number(n).toLocaleString('id-ID'); }

/* ---------- Seed data ---------- */
let pesananData = loadData('le_pesanan', [
  { id: uid(), layanan: 'Cuci Kiloan', tanggal: '2026-09-27', status: 'proses' },
  { id: uid(), layanan: 'Sewa Peralatan', tanggal: '2026-09-28', status: 'tunggu' },
  { id: uid(), layanan: 'Dry Cleaning', tanggal: '2026-09-25', status: 'selesai' },
]);
let alatData = loadData('le_alat', [
  { id: uid(), nama: 'Vacuum Cleaner', stok: 3, harga: 25000, status: 'tersedia' },
  { id: uid(), nama: 'Setrika Uap', stok: 2, harga: 15000, status: 'disewa' },
]);

/* ---------- Render ---------- */
function renderPesanan() {
  const body = document.getElementById('pesananTableBody');
  body.innerHTML = '';
  if (pesananData.length === 0) {
    body.innerHTML = '<tr class="empty-row"><td colspan="4">Belum ada data pesanan.</td></tr>';
  } else {
    pesananData.forEach(item => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${item.layanan}</td>
        <td>${formatTanggal(item.tanggal)}</td>
        <td><span class="badge ${STATUS_CLASS[item.status]}">${STATUS_LABEL[item.status]}</span></td>
        <td class="row-actions">
          <button data-edit="${item.id}">Edit</button>
          <button data-del="${item.id}" class="del">Hapus</button>
        </td>`;
      body.appendChild(tr);
    });
  }
  document.getElementById('statAktif').textContent = pesananData.filter(p => p.status !== 'selesai').length;
  document.getElementById('statSelesai').textContent = pesananData.filter(p => p.status === 'selesai').length;
}

function renderAlat() {
  const body = document.getElementById('alatTableBody');
  body.innerHTML = '';
  if (alatData.length === 0) {
    body.innerHTML = '<tr class="empty-row"><td colspan="5">Belum ada data peralatan.</td></tr>';
  } else {
    alatData.forEach(item => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${item.nama}</td>
        <td>${item.stok}</td>
        <td>${formatRupiah(item.harga)}</td>
        <td><span class="badge ${ALAT_STATUS_CLASS[item.status]}">${ALAT_STATUS_LABEL[item.status]}</span></td>
        <td class="row-actions">
          <button data-edit="${item.id}">Edit</button>
          <button data-del="${item.id}" class="del">Hapus</button>
        </td>`;
      body.appendChild(tr);
    });
  }
  document.getElementById('statAlat').textContent = alatData.filter(a => a.status === 'disewa').length;
}

/* ---------- Modal helpers ---------- */
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('[data-close]').forEach(btn => {
  btn.addEventListener('click', () => closeModal(btn.dataset.close));
});
document.querySelectorAll('.modal-backdrop').forEach(bg => {
  bg.addEventListener('click', e => { if (e.target === bg) bg.classList.remove('open'); });
});

/* ---------- Pesanan CRUD ---------- */
const pesananForm = document.getElementById('pesananForm');

document.getElementById('btnAddPesanan').addEventListener('click', () => {
  pesananForm.reset();
  document.getElementById('pesananId').value = '';
  document.getElementById('pesananModalTitle').textContent = 'Tambah Pesanan';
  openModal('pesananModal');
});

document.getElementById('pesananTableBody').addEventListener('click', e => {
  const editId = e.target.dataset.edit;
  const delId = e.target.dataset.del;
  if (editId) {
    const item = pesananData.find(p => p.id === editId);
    if (!item) return;
    document.getElementById('pesananId').value = item.id;
    document.getElementById('pesananLayanan').value = item.layanan;
    document.getElementById('pesananTanggal').value = item.tanggal;
    document.getElementById('pesananStatus').value = item.status;
    document.getElementById('pesananModalTitle').textContent = 'Edit Pesanan';
    openModal('pesananModal');
  }
  if (delId) {
    if (confirm('Hapus data pesanan ini?')) {
      pesananData = pesananData.filter(p => p.id !== delId);
      saveData('le_pesanan', pesananData);
      renderPesanan();
    }
  }
});

pesananForm.addEventListener('submit', e => {
  e.preventDefault();
  const id = document.getElementById('pesananId').value;
  const payload = {
    layanan: document.getElementById('pesananLayanan').value,
    tanggal: document.getElementById('pesananTanggal').value,
    status: document.getElementById('pesananStatus').value,
  };
  if (id) {
    pesananData = pesananData.map(p => p.id === id ? { ...p, ...payload } : p);
  } else {
    pesananData.push({ id: uid(), ...payload });
  }
  saveData('le_pesanan', pesananData);
  renderPesanan();
  closeModal('pesananModal');
});

/* ---------- Alat CRUD ---------- */
const alatForm = document.getElementById('alatForm');

document.getElementById('btnAddAlat').addEventListener('click', () => {
  alatForm.reset();
  document.getElementById('alatId').value = '';
  document.getElementById('alatModalTitle').textContent = 'Tambah Alat';
  openModal('alatModal');
});

document.getElementById('alatTableBody').addEventListener('click', e => {
  const editId = e.target.dataset.edit;
  const delId = e.target.dataset.del;
  if (editId) {
    const item = alatData.find(a => a.id === editId);
    if (!item) return;
    document.getElementById('alatId').value = item.id;
    document.getElementById('alatNama').value = item.nama;
    document.getElementById('alatStok').value = item.stok;
    document.getElementById('alatHarga').value = item.harga;
    document.getElementById('alatStatus').value = item.status;
    document.getElementById('alatModalTitle').textContent = 'Edit Alat';
    openModal('alatModal');
  }
  if (delId) {
    if (confirm('Hapus data alat ini?')) {
      alatData = alatData.filter(a => a.id !== delId);
      saveData('le_alat', alatData);
      renderAlat();
    }
  }
});

alatForm.addEventListener('submit', e => {
  e.preventDefault();
  const id = document.getElementById('alatId').value;
  const payload = {
    nama: document.getElementById('alatNama').value,
    stok: Number(document.getElementById('alatStok').value),
    harga: Number(document.getElementById('alatHarga').value),
    status: document.getElementById('alatStatus').value,
  };
  if (id) {
    alatData = alatData.map(a => a.id === id ? { ...a, ...payload } : a);
  } else {
    alatData.push({ id: uid(), ...payload });
  }
  saveData('le_alat', alatData);
  renderAlat();
  closeModal('alatModal');
});

/* ---------- Init ---------- */
renderPesanan();
renderAlat();
