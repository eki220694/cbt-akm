<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TemplateDownloadController extends Controller
{
    /**
     * Mengatur pembuatan file Excel template kosong secara aman via HTTP GET murni
     */
    public function download(string $module): StreamedResponse
    {
        return match ($module) {
            "majors" => SimpleExcelWriter::streamDownload(
                "template_jurusan.xlsx",
            )
                ->addHeader(["code", "name"])
                ->addRow([
                    "code" => "MIPA",
                    "name" => "Matematika dan Ilmu Pengetahuan Alam",
                ])
                ->addRow(["code" => "IPS", "name" => "Ilmu Pengetahuan Sosial"])
                ->addRow([
                    "code" => "TKJ",
                    "name" => "Teknik Komputer dan Jaringan",
                ]),

            "exam_sessions" => SimpleExcelWriter::streamDownload(
                "template_sesi.xlsx",
            )
                ->addHeader(["name", "start_time", "end_time"])
                ->addRow([
                    "name" => "Sesi 1",
                    "start_time" => "07:30",
                    "end_time" => "09:30",
                ])
                ->addRow([
                    "name" => "Sesi 2",
                    "start_time" => "10:00",
                    "end_time" => "12:00",
                ])
                ->addRow([
                    "name" => "Sesi 3",
                    "start_time" => "13:00",
                    "end_time" => "15:00",
                ]),

            "classrooms" => SimpleExcelWriter::streamDownload(
                "template_kelas.xlsx",
            )
                ->addHeader(["name", "exam_session_name"])
                ->addRow(["name" => "X-1", "exam_session_name" => "Sesi 1"])
                ->addRow([
                    "name" => "XI-MIPA-1",
                    "exam_session_name" => "Sesi 2",
                ])
                ->addRow([
                    "name" => "XII-IPS-2",
                    "exam_session_name" => "Sesi 1",
                ]),

            "questions" => SimpleExcelWriter::streamDownload(
                "template_soal_akm.xlsx",
            )
                ->addHeader([
                    "content",
                    "type",
                    "points",
                    "answer_key",
                    "options_json_format",
                ])
                ->addRow([
                    "content" => "Berapakah hasil dari 2 + 2?",
                    "type" => "pg",
                    "points" => 1,
                    "answer_key" => "A",
                    "options_json_format" =>
                        '[{"key":"A","value":"4"},{"key":"B","value":"5"}]',
                ])
                ->addRow([
                    "content" =>
                        "Jelaskan dampak pemanasan global bagi pertanian Sigi!",
                    "type" => "essay",
                    "points" => 2,
                    "answer_key" => null,
                    "options_json_format" => null,
                ]),

            default => abort(404),
        };
    }
}
