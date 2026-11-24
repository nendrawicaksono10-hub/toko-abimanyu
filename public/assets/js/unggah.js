/**
 * unggah.js - pratinjau & pemeriksaan awal berkas gambar (ImgPratinjau, ImgPratinjauBukti).
 * Validasi sesungguhnya tetap dilakukan server (tipe dari isi berkas, ukuran).
 */
(function () {
  'use strict';
  const TIPE = ['image/jpeg', 'image/png', 'image/webp'];
  document.addEventListener('DOMContentLoaded', () => {
    const { toast } = window.Abimanyu;

    document.querySelectorAll('input[data-input-gambar]').forEach((input) => {
      const wadah = input.closest('[data-unggah]');
      const img = wadah.querySelector('[data-pratinjau]');
      const nama = wadah.querySelector('[data-nama-berkas]');
      const ikon = wadah.querySelector('[data-ikon-unggah]');
      const maks = Number(input.dataset.maksMb || 2) * 1024 * 1024;
      input.addEventListener('change', () => {
        const f = input.files[0];
        if (!f) return;
        if (!TIPE.includes(f.type)) { toast('Berkas harus berupa gambar JPG, PNG, atau WebP.', 'gagal'); input.value = ''; return; }
        if (f.size > maks) { toast('Ukuran berkas melebihi ' + input.dataset.maksMb + ' MB.', 'gagal'); input.value = ''; return; }
        img.src = URL.createObjectURL(f);
        img.classList.remove('hidden');
        ikon && ikon.classList.add('hidden');
        nama.textContent = f.name + ' · ' + (f.size / 1024 / 1024).toFixed(2) + ' MB';
      });
    });

    document.querySelectorAll('input[data-input-banyak]').forEach((input) => {
      const wadah = input.closest('[data-unggah-banyak]');
      const grid = wadah.querySelector('[data-pratinjau-banyak]');
      const nama = wadah.querySelector('[data-nama-berkas]');
      const maksJumlah = Number(input.dataset.maks || 4);
      const maks = Number(input.dataset.maksMb || 2) * 1024 * 1024;
      input.addEventListener('change', () => {
        grid.innerHTML = '';
        const files = Array.from(input.files);
        if (files.length > maksJumlah) { toast('Maksimal ' + maksJumlah + ' foto tambahan lagi (total 5 foto per produk).', 'gagal'); input.value = ''; nama.textContent = 'Pilih hingga ' + maksJumlah + ' foto tambahan'; return; }
        for (const f of files) {
          if (!TIPE.includes(f.type) || f.size > maks) { toast('"' + f.name + '" bukan JPG/PNG/WebP atau melebihi ' + input.dataset.maksMb + ' MB.', 'gagal'); input.value = ''; grid.innerHTML = ''; return; }
          const im = document.createElement('img');
          im.src = URL.createObjectURL(f);
          im.className = 'aspect-square w-full object-cover rounded-lg border border-border-warm';
          grid.appendChild(im);
        }
        nama.textContent = files.length ? files.length + ' foto dipilih' : 'Pilih foto tambahan';
      });
    });
  });
})();
