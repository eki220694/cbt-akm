# Panduan Offline CBT-AKM: Instalasi → 60 Siswa via WiFi

## 1. Siapkan bundle (online sekali, di Linux ini)

```bash
./deploy/offline/bundle-online.sh
```

Hasil: `deploy/offline/apt-cache/*.deb` (nginx, php8.3-fpm, mysql-server, ekstensi)
+ `cbt-akm-offline-bundle.tar.gz` (kode + vendor + build).
Copy seluruh folder repo ke server via USB.

## 2. Buat VM di server (VirtualBox)

- Ubuntu Server 24.04, 4 vCPU, 8 GB RAM, 40 GB disk.
- Adapter 1 NAT (opsional), Adapter 2 Bridged (agar HP satu WiFi akses)
  atau Host-only (hanya admin laptop).
- Masukkan repo via Shared Folder / USB. Taruh di `/var/www/cbt-akm`.

## 3. Install app offline (di VM, tanpa internet)

```bash
cd /var/www/cbt-akm
./deploy/offline/install-offline.sh --db=mysql --no-serve --skip-tests
```

Skrip lakukan: `dpkg -i apt-cache/*.deb`, extract bundle, buat DB `cbt_akm`,
`key:generate`, `migrate`, `seed`. Cek IP:

```bash
ip a
```

Contoh: `192.168.1.50`.

## 4. Aktifkan produksi (Nginx + FPM + MySQL)

```bash
sudo ./deploy/offline/setup-prod.sh
```

Skrip lakukan: enable `php8.3-fpm` + `nginx` + `mysql`, buat DB bila belum ada,
`config:cache` + `route:cache` + `view:cache`, `chown www-data`.
Pool FPM statis 40 anak + OPcache 256 MB, cukup 60 siswa.
Tes dari laptop: `http://192.168.1.50`.

## 5. WiFi agar HP akses

- Opsi mudah: HP + server satu router (tanpa internet tidak masalah).
  Server pakai kabel LAN, IP statik `192.168.1.50`.
- Tanpa router: hotspot dari laptop server, HP hubung ke situ.
- 60 HP: tambah 1 access point, SSID sama, kanal beda (1 dan 6), lebar 20 MHz.

## 6. Admin siapkan ujian (browser laptop)

1. `http://IP-VM/admin`. Login admin.
2. Isi Mapel (`subjects`), Kelas, Sesi, Bank Soal (impor `.xlsx` via template,
   kolom `subject_code` ikut).
3. Daftarkan siswa: `NISN` (unik, jadi username) + password + kelas.
4. Buat progress ujian per siswa (otomatis `session_token`, `order_seed`
   acak soal/opsi stabil, status `not_started` → `in_progress`).
5. Peran: guru boleh kelola soal + lihat hasil, tidak hapus sesi/kelola user.
   Akun nonaktif (`is_active=false`) ditolak panel.

## 7. Siswa ujian (HP)

1. Buka `http://192.168.1.50/student/login`. Login NISN + password.
2. Dashboard → pilih ujian → kerjakan. Timer server-side + auto-submit.
3. Kumpul → nilai tampil. PG/isian/benar-salah otomatis.
   PG kompleks/menjodohkan multi-kunci otomatis. Essay 0, tunggu koreksi guru.
4. Login bertahap 10 menit awal, jangan refresh bareng. Submit gagal:
   tunggu 10 detik, Kumpulkan lagi (jawaban per soal sudah tersimpan).

## 8. Guru koreksi + rekap (laptop)

1. `Hasil Ujian` → pilih siswa → tab Jawaban → koreksi essay
   (isi `points` + `is_correct`, skor recalc otomatis).
2. Halaman `Analisis Butir`: % benar per soal per sesi untuk revisi soal.
3. Ekspor `.xlsx` rekap nilai.
4. Backup tiap selesai: `mysqldump cbt_akm > backup.sql`, simpan ke USB.
