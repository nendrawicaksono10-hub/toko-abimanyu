/**
 * keranjang.js - Mengelola Keranjang Belanja (FR-06) via AJAX JSON.
 * Tanpa JavaScript, seluruh form tetap berfungsi (kirim biasa).
 */
(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', () => {
    const { $, $$, toast, kirimForm } = window.Abimanyu;

    const perbaruiBadge = (n) => $$('[data-keranjang-count]').forEach((b) => { b.textContent = n; b.classList.toggle('hidden', !n); });

    const perbaruiRingkasan = (d) => {
      $$('[data-total]').forEach((el) => { el.textContent = d.total; });
      $$('[data-total-unit]').forEach((el) => { el.textContent = d.jumlahUnit; });
      const btn = $('[data-btn-checkout]');
      if (btn) { btn.classList.toggle('opacity-40', d.adaMasalah); btn.classList.toggle('pointer-events-none', d.adaMasalah); }
      const p = $('[data-peringatan-checkout]');
      p && p.classList.toggle('hidden', !d.adaMasalah);
      Object.entries(d.items || {}).forEach(([id, it]) => {
        const row = $(`[data-item="${id}"]`);
        if (!row) return;
        $('[data-subtotal]', row).textContent = it.subtotal;
        const m = $('[data-masalah]', row);
        if (m) { m.textContent = it.masalah || ''; m.classList.toggle('hidden', !it.masalah); }
      });
      const jp = $('[data-jumlah-produk]');
      jp && (jp.textContent = Object.keys(d.items || {}).length + ' produk');
    };

    // Tambah ke keranjang (detail produk / rekomendasi)
    $$('form[data-tambah-keranjang]').forEach((f) => f.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = $('button[type=submit]', f);
      btn && (btn.disabled = true);
      const r = await kirimForm(f);
      btn && (btn.disabled = false);
      if (r.data && typeof r.data.jumlahKeranjang !== 'undefined') perbaruiBadge(r.data.jumlahKeranjang);
      toast(r.message || (r.status === 'success' ? 'Ditambahkan ke keranjang.' : 'Gagal.'), r.status === 'success' ? 'sukses' : 'gagal');
    }));

    // Ubah jumlah (stepper) - kirim setelah jeda singkat
    $$('form[data-ubah-jumlah]').forEach((f) => {
      const input = $('input[name=jumlah]', f);
      let nilaiSah = input.value, t;
      const kirim = async () => {
        const r = await kirimForm(f);
        if (r.status === 'success') {
          nilaiSah = input.value;
          perbaruiBadge(r.data.jumlahKeranjang);
          perbaruiRingkasan(r.data);
        } else {
          input.value = nilaiSah; // isi keranjang tidak berubah
          toast(r.message || 'Jumlah tidak dapat diubah.', 'gagal');
        }
      };
      input.addEventListener('change', () => { clearTimeout(t); t = setTimeout(kirim, 350); });
      f.addEventListener('submit', (e) => { e.preventDefault(); kirim(); });
    });

    // Hapus item
    $$('form[data-hapus-item]').forEach((f) => f.addEventListener('submit', async (e) => {
      e.preventDefault();
      const r = await kirimForm(f);
      if (r.status !== 'success') { toast(r.message, 'gagal'); return; }
      if (r.data.kosong) { window.location.reload(); return; }
      f.closest('[data-item]').remove();
      perbaruiBadge(r.data.jumlahKeranjang);
      perbaruiRingkasan(r.data);
      toast(r.message);
    }));
  });
})();
