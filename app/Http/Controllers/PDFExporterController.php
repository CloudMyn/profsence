<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFExporterController extends Controller
{
    public function exportPelanggaran(User $user)
    {

        $data_user  =   [
            'id'        =>  "#" . $user->id,
            'nama'      =>  $user->name,
            'peran'     =>  $user->role,
            'email'     =>  $user->email,
        ];
        $check_in_count     =   $user->attendances()->whereBetween('created_at', [
            now()->startOfMonth()->format('Y-m-d'),
            now()->endOfMonth()->format('Y-m-d')
        ])->where('type', 'check_in')->count();

        $check_out_count    =   $user->attendances()->whereBetween('created_at', [
            now()->startOfMonth()->format('Y-m-d'),
            now()->endOfMonth()->format('Y-m-d')
        ])->where('type', 'check_out')->count();

        $violdation_count   =   $user->attendances()->whereBetween('created_at', [
            now()->startOfMonth()->format('Y-m-d'),
            now()->endOfMonth()->format('Y-m-d')
        ])->where('check_violation', true)->count();

        $cuti_count         =   $user->attendances()->whereBetween('created_at', [
            now()->startOfMonth()->format('Y-m-d'),
            now()->endOfMonth()->format('Y-m-d')
        ])->where('type', 'cuti')->count();

        $sakit_count        =   $user->attendances()->whereBetween('created_at', [
            now()->startOfMonth()->format('Y-m-d'),
            now()->endOfMonth()->format('Y-m-d')
        ])->where('type', 'sakit')->count();

        $dinas_luar_count   =   $user->attendances()->whereBetween('created_at', [
            now()->startOfMonth()->format('Y-m-d'),
            now()->endOfMonth()->format('Y-m-d')
        ])->where('type', 'dinas_luar')->count();

        $x  =   " ×";

        $h  =   " Hari";

        $laporan_absensi    =   [
            'Absen Masuk'       =>  $check_in_count . $x,
            'Absen Keluar'      =>  $check_out_count . $x,
            'Pelanggaran Absen' =>  $violdation_count . $x,
            'Izin Cuti'         =>  $cuti_count . $h,
            'Izin Sakit'        =>  $sakit_count . $h,
            'Izin Dinas'        =>  $dinas_luar_count . $h,
        ];

        $violdations    =   $user->attendances()->whereBetween('created_at', [
                now()->startOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d')
            ])->where('check_violation', true)->get();

        $formated_table =   [];

        $index  =   1;
        foreach ($violdations as $absence) {
            $formated_table[] =   [
                $index,
                $absence->violation_note,
                $absence->note,
                $absence->created_at->format('H:i'),
                $absence->created_at->format('d/m/Y'),
            ];

            $index++;
        }

        $data   =   [
            'title'     =>  'Laporan Absensi Pengguna',
            'content'   =>  [
                'Data Pengguna'     =>  $data_user,
                'Laporan Absensi Periode ' . date('F Y')   =>  $laporan_absensi
            ],
            'tables'    =>  [
                'List Pelanggaran Bulan ini'    =>  [
                    "kolom"     =>  ['No', 'Pelanggaran', 'Keterangan', 'Jam', 'Tanggal'],
                    "data"      =>  $formated_table,
                ]
            ]
        ];

        $pdf = Pdf::loadView('reporting.template_list', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }
}
