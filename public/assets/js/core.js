/**
 * core.js - utilitas bersama (vanilla JS, tanpa framework).
 * Toast, permintaan AJAX JSON, dialog konfirmasi, menu, salin teks, dll.
 */
(function () {
  'use strict';
  const $ = (s, el = document) => el.querySelector(s);
  const $$ = (s, el = document) => Array.from(el.querySelectorAll(s));

  // ---------------- Toast ----------------
  let toastTimer;
  function toast(pesan, jenis = 'sukses') {
    const el = $('#toast') || document.body.appendChild(Object.assign(document.createElement('div'), { id: 'toast', className: 'fixed bottom-5 left-1/2 -translate-x-1/2 z-[70]' }));
    const gagal = jenis === 'gagal';
    el.innerHTML = '';
    const box = document.createElement('div');
    box.className = 'flex items-center gap-3 px-5 py-3 rounded-full border shadow-[0_8px_30px_rgba(0,0,0,0.08)] font-label-sm text-label-sm max-w-[92vw] ' +
      (gagal ? 'bg-error-container text-on-error-container border-error-container' : 'bg-primary text-on-primary border-primary');
    const ikon = document.createElement('span');
    ikon.className = 'material-symbols-outlined text-[18px]';
    ikon.textContent = gagal ? 'error' : 'check_circle';
    const teks = document.createElement('span');
    teks.textContent = pesan;
    box.append(ikon, teks);
    el.appendChild(box);
    el.classList.remove('hidden');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.add('hidden'), 3200);
  }

  // ---------------- AJAX JSON {status, data, message} ----------------
  async function kirimForm(form) {
    const res = await fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    });
    let json;
    try { json = await res.json(); } catch (e) { json = { status: 'error', message: 'Terjadi kesalahan. Silakan muat ulang halaman.' }; }
    return json;
  }

  // ---------------- Dialog konfirmasi (pengganti confirm()) ----------------
  function konfirmasi(pesan, labelYa = 'Ya, lanjutkan') {
    return new Promise((resolve) => {
      const latar = document.createElement('div');
      latar.className = 'fixed inset-0 z-[80] bg-ink/40 flex items-center justify-center p-4';
      latar.setAttribute('role', 'alertdialog');
      latar.innerHTML = '<div class="w-full max-w-sm bg-surface-card rounded-2xl border border-border-warm p-6 flex flex-col gap-4">' +
        '<div class="flex items-start gap-3"><span class="material-symbols-outlined text-[24px]">help</span><p class="font-body-md text-body-md" data-pesan></p></div>' +
        '<div class="flex justify-end gap-2"><button type="button" class="btn-sekunder btn-kecil" data-tidak>Batal</button><button type="button" class="btn-primer btn-kecil" data-ya></button></div></div>';
      $('[data-pesan]', latar).textContent = pesan;
      $('[data-ya]', latar).textContent = labelYa;
      const selesai = (v) => { latar.remove(); document.removeEventListener('keydown', esc); resolve(v); };
      const esc = (e) => { if (e.key === 'Escape') selesai(false); };
      latar.addEventListener('click', (e) => { if (e.target === latar) selesai(false); });
      $('[data-tidak]', latar).addEventListener('click', () => selesai(false));
      $('[data-ya]', latar).addEventListener('click', () => selesai(true));
      document.addEventListener('keydown', esc);
      document.body.appendChild(latar);
      $('[data-ya]', latar).focus();
    });
  }

  window.Abimanyu = { $, $$, toast, kirimForm, konfirmasi };

  // Gambar produk gagal dimuat -> placeholder (pengganti onerror inline, aman untuk CSP)
  document.addEventListener('error', (e) => {
    const img = e.target;
    if (!(img instanceof HTMLImageElement) || !img.hasAttribute('data-fallback')) return;
    const div = document.createElement('div');
    div.className = 'w-full h-full flex items-center justify-center bg-surface-subtle text-outline';
    const ikon = document.createElement('span');
    ikon.className = 'material-symbols-outlined text-[48px]';
    ikon.textContent = 'image_not_supported';
    div.appendChild(ikon);
    img.replaceWith(div);
  }, true);

  document.addEventListener('DOMContentLoaded', () => {
    // Tutup notifikasi flash
    $$('[data-tutup-flash]').forEach((b) => b.addEventListener('click', () => b.closest('[data-flash]').remove()));

    // Toggle menu (header seluler)
    $$('[data-toggle]').forEach((b) => b.addEventListener('click', () => {
      const t = $(b.dataset.toggle);
      if (!t) return;
      const buka = t.classList.toggle('hidden') === false;
      b.setAttribute('aria-expanded', String(buka));
    }));

    // Sidebar admin (seluler)
    const sidebar = $('#sidebar'), latar = $('#sidebar-latar');
    const setSidebar = (buka) => { if (!sidebar) return; sidebar.classList.toggle('-translate-x-full', !buka); latar && latar.classList.toggle('hidden', !buka); };
    $$('[data-buka-sidebar]').forEach((b) => b.addEventListener('click', () => setSidebar(true)));
    $$('[data-tutup-sidebar]').forEach((b) => b.addEventListener('click', () => setSidebar(false)));

    // Kirim otomatis saat <select> berubah
    $$('form[data-auto-submit] select').forEach((s) => s.addEventListener('change', () => s.form.submit()));

    // Salin teks ke clipboard (BtnSalinRekening)
    $$('[data-salin]').forEach((b) => b.addEventListener('click', async () => {
      try { await navigator.clipboard.writeText(b.dataset.salin); toast('Disalin: ' + b.dataset.salin); }
      catch (e) { toast('Gagal menyalin. Salin secara manual.', 'gagal'); }
    }));

    // Tampilkan / sembunyikan password (BtnTampilPass)
    $$('[data-tampil-password]').forEach((b) => b.addEventListener('click', () => {
      const i = $(b.dataset.tampilPassword);
      if (!i) return;
      i.type = i.type === 'password' ? 'text' : 'password';
      $('.material-symbols-outlined', b).textContent = i.type === 'password' ? 'visibility' : 'visibility_off';
    }));

    // Cegah pengiriman ganda (double submit)
    $$('form[data-cegah-ganda]').forEach((f) => f.addEventListener('submit', (e) => {
      if (f.dataset.terkirim === '1') { e.preventDefault(); return; }
      f.dataset.terkirim = '1';
      $$('[data-tombol-kirim]', f).forEach((b) => { b.disabled = true; b.classList.add('opacity-60'); });
      setTimeout(() => { f.dataset.terkirim = ''; $$('[data-tombol-kirim]', f).forEach((b) => { b.disabled = false; b.classList.remove('opacity-60'); }); }, 15000);
    }));

    // Konfirmasi sebelum hapus / tindakan penting
    $$('form[data-konfirmasi]').forEach((f) => f.addEventListener('submit', async (e) => {
      if (f.dataset.ok === '1') return;
      e.preventDefault();
      if (await konfirmasi(f.dataset.konfirmasi, 'Ya, hapus')) { f.dataset.ok = '1'; f.submit(); }
    }));
    $$('form[data-konfirmasi-pilihan]').forEach((f) => f.addEventListener('submit', async (e) => {
      const opt = f.querySelector('select option:checked');
      const pesan = opt && opt.dataset.peringatan;
      if (!pesan || f.dataset.ok === '1') return;
      e.preventDefault();
      if (await konfirmasi(pesan, 'Ya, batalkan pesanan')) { f.dataset.ok = '1'; f.submit(); }
    }));

    // Stepper jumlah (+/-)
    $$('[data-stepper]').forEach((w) => {
      const input = $('input[type=number]', w);
      $$('[data-step]', w).forEach((b) => b.addEventListener('click', () => {
        const min = Number(b.dataset.min ?? input.min ?? 1) || 0;
        const max = input.max ? Number(input.max) : Infinity;
        const v = Math.min(max, Math.max(min, (parseInt(input.value, 10) || 0) + Number(b.dataset.step)));
        if (String(v) !== input.value) { input.value = v; input.dispatchEvent(new Event('change', { bubbles: true })); }
      }));
    });

    // Modal: tutup dengan ESC / klik latar
    $$('[data-modal]').forEach((m) => {
      const tutup = () => { window.location.href = m.dataset.tutupKe; };
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') tutup(); });
      m.addEventListener('click', (e) => { if (e.target === m) tutup(); });
      document.body.classList.add('overflow-hidden');
    });
  });
})();
