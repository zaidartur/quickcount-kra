<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToArray;

use Illuminate\Support\Facades\DB;

// class DptImport implements OnEachRow, WithChunkReading, WithHeadingRow, WithEvents, ShouldQueue
class DptImport implements ToArray, WithChunkReading, WithHeadingRow
{
    public $duplicate = [];
    public $template = '';
    public function array(array $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['kodekabalamat']) || !isset($row['kec']) || !isset($row['desakel']) || !isset($row['jumlah_dpt']) || !isset($row['nomor_tps'])) {
                $this->template = 'Template file tidak sesuai';
                break;
            } else {
                $iskec = DB::table('data_kecamatan')->where('kec_name', ucfirst(strtolower($row['kec'])))->first();
                if ($iskec) {
                    $desa = DB::table('data_desa')->where('desakel_name', ucfirst(strtolower($row['desakel'])))->where('kec_id', $iskec->kec_id)->first();
                    if ($desa) {
                        $kecamatan = $desa->kec_id;
                        DB::table('data_dpt')->insert([
                            'kotakab_id'    => 13,
                            'kec_id'        => $kecamatan,
                            // 'desakel_id'    => $row['kode_desa'],
                            // 'full_id'       => 13 . $kecamatan . $row['kode_desa'],
                            'desakel_id'    => $desa->desakel_id,
                            'full_id'       => $desa->full_id,
                            'kpu_id'        => $row['kodekabalamat'],
                            'no_tps'        => intval($row['nomor_tps']),
                            'tahun_dpt'     => date('Y'),
                            'total'         => $row['jumlah_dpt'],
                            'created_at'    => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
