# CBT-AKM

Aplikasi Computer-Based Test Asesmen Kompetensi Minimum (AKM) untuk sekolah.
Panel admin: Filament. Bank soal: TinyMCE + Wiris MathType. Impor/ekspor: template Excel per modul.

## Stack

- Laravel 13, PHP 8.3, Filament 5
- MySQL 8.4 / Redis / Mailpit via Laravel Sail (`compose.yaml`)
- Spatie SimpleExcel (template `.xlsx`), Tailwind 4 + Vite

## Modul

| Modul | Resource | Template |
|---|---|---|
| Jurusan | Data Jurusan | `code, name` |
| Sesi ujian | Data Sesi | `name, start_time (H:i), end_time (H:i)` |
| Kelas | Data Kelas | `name, exam_session_name` |
| Soal AKM | Data Soal AKM | `content, type, points, answer_key, options_json_format` |

Tipe soal: `pg, pg_kompleks, isian_singkat, essay, menjodohkan`.
Unduh template: `GET /admin/templates/{module}/download` (perlu login).

## Jalan lokal (Sail)

```bash
cp .env.example .env   # sesuaikan blok Sail bila perlu
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
npm install && npm run dev
```

Buka `http://localhost/admin`.

## Jalan lokal (sqlite, tanpa docker)

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run dev
```

Default `.env.example` pakai sqlite. Untuk Sail, ubah `DB_*`/`REDIS_*`/`MAIL_*` sesuai komentar di file.

## Test & gaya kode

```bash
php artisan test
./vendor/bin/pint --test
```

Test domain: `tests/Feature/ImporterLogicTest.php` (impor, template auth, bulk assign, cast soal).

## Rutinitas dua mesin

```bash
git fetch origin && git pull --ff-only origin main   # mulai kerja
git add -A && git commit -m "pesan" && git push origin main   # selesai kerja
```

Remote pakai SSH (`git@github.com:eki220694/cbt-akm.git`), satu key per mesin.
Jangan taruh token di URL remote.

## Lisensi

MIT.
