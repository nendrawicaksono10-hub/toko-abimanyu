<?php /** Halaman Kebijakan Privasi (FR-41, UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi). */ ?>
<div class="wadah pt-space-lg">
  <article class="max-w-3xl mx-auto">
    <nav class="font-caption text-caption text-secondary mb-space-md" aria-label="Breadcrumb"><a href="<?= e(url('/')) ?>" class="hover:text-ink">Beranda</a> <span class="mx-1">/</span> <span class="text-ink font-semibold">Kebijakan Privasi</span></nav>
    <h1 class="font-display text-headline-lg-mobile md:text-headline-lg text-ink">Kebijakan Privasi</h1>
    <p class="font-body-md text-body-md text-secondary mt-2">Kebijakan ini menjelaskan bagaimana Toko Abimanyu mengumpulkan, menggunakan, dan melindungi data pribadi Anda sesuai Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi.</p>
    <div class="mt-space-xl flex flex-col gap-space-lg font-body-md text-body-md text-on-surface">
      <?php foreach ([
          ['Data yang Kami Kumpulkan', 'Saat Anda membuat pesanan, kami mengumpulkan nama, nomor WhatsApp/telepon, dan alamat pengiriman. Saat Anda mengonfirmasi pembayaran, kami menyimpan nama pengirim, tanggal pembayaran, dan berkas bukti pembayaran. Kami tidak menyimpan data kartu atau akun keuangan Anda.'],
          ['Tujuan Penggunaan', 'Data digunakan hanya untuk memproses dan mengirim pesanan, memverifikasi pembayaran, menghubungi Anda terkait pesanan (termasuk melalui WhatsApp), serta menyusun laporan penjualan internal.'],
          ['Akses dan Keamanan', 'Data pesanan hanya dapat diakses oleh admin Toko Abimanyu yang berwenang. Bukti pembayaran disimpan di lokasi yang tidak dapat diakses publik dan hanya ditampilkan kepada admin. Halaman cek status hanya menampilkan pesanan bila nomor pesanan dan nomor kontak cocok.'],
          ['Penyimpanan', 'Data disimpan selama diperlukan untuk keperluan transaksi, layanan purnajual, dan pencatatan usaha.'],
          ['Hak Anda', 'Anda berhak meminta informasi, perbaikan, atau penghapusan data pribadi Anda yang tidak lagi diperlukan. Sampaikan permintaan melalui WhatsApp admin.'],
          ['Persetujuan', 'Dengan mencentang kotak persetujuan pada form pemesanan, Anda menyetujui penggunaan data pribadi sebagaimana dijelaskan dalam kebijakan ini.'],
      ] as $i => [$judul, $isi]): ?>
        <section>
          <h2 class="font-display text-headline-sm text-ink"><?= $i + 1 ?>. <?= e($judul) ?></h2>
          <p class="mt-2 text-secondary"><?= e($isi) ?></p>
        </section>
      <?php endforeach; ?>
      <section class="kartu-muted p-space-lg">
        <h2 class="font-title-md text-title-md text-ink">Kontak</h2>
        <p class="mt-1 text-secondary">Pertanyaan tentang kebijakan ini dapat disampaikan melalui <a href="<?= e(waLink('Halo Toko Abimanyu, saya ingin bertanya tentang kebijakan privasi.')) ?>" target="_blank" rel="noopener" class="underline text-ink">WhatsApp admin</a>.</p>
      </section>
    </div>
  </article>
</div>
