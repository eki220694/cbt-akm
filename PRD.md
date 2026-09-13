# PRD — CBT AKM

## Produk
Aplikasi ujian berbasis komputer untuk Asesmen Kompetensi Minimum (AKM).
Bank soal dengan stimulus, sesi ujian dinamis, impor/ekspor XLSX, alur siswa, cetak PDF.

## Peran
- `admin` — akses penuh panel Filament, kelola semua modul, cetak dokumen.
- `guru` — akses panel Filament, kelola soal dan sesi.
- Siswa (`auth:student`) — login NIS, kerjakan ujian, lihat hasil. Tanpa akses panel admin.

## Modul
- **Jurusan (Major)** — data jurusan.
- **Kelas (Classroom)** — data kelas, relasi ke sesi ujian.
- **Bank Soal (Question)** — kolom: stimulus, konten, tipe (enum), poin, kunci jawaban, opsi (JSON). Relasi many-to-many ke sesi via pivot `exam_session_question`.
- **Sesi Ujian (ExamSession)** — jadwal, relasi kelas + soal (pivot).
- **Siswa (Student)** — login via guard `student`.
- **Progres Ujian (StudentExamProgress)** — status pengerjaan siswa per sesi.
- **Impor/Ekspor** — template XLSX per modul + unduhan langsung (bypass Livewire).
- **Cetak PDF** — kartu ujian, daftar hadir, berita acara.
- **Alur Siswa** — login → dashboard → kerjakan → kumpul → hasil.

## Tipe Soal (`QuestionType`)
| Nilai | Label |
|---|---|
| `pg` | Pilihan Ganda |
| `pg_kompleks` | Pilihan Ganda Kompleks |
| `isian_singkat` | Isian Singkat |
| `essay` | Essay/Uraian |
| `menjodohkan` | Menjodohkan |

Penilaian otomatis: PG, PG kompleks, isian singkat. Essay dan menjodohkan butuh koreksi manual (belum ada).

## Rute Utama
- `/admin` — panel Filament (admin, guru).
- `/admin/templates/{module}/download` — unduh template XLSX (`majors`, `exam_sessions`, `classrooms`, `questions`).
- `/student/login` (GET+POST) — login siswa.
- `/student/dashboard` — daftar sesi siswa.
- `/student/exam/{progress}/take` (GET), `/submit` (POST), `/result` (GET) — kerjakan dan lihat hasil.
- `/student/logout` (POST) — keluar.
- `/cetak/kartu-ujian`, `/cetak/daftar-hadir`, `/cetak/berita-acara` — cetak PDF (login panel).
- `/` — halaman welcome.
