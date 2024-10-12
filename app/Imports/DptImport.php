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
            if (!isset($row['kode_kecamatan']) || !isset($row['kode_desa']) || !isset($row['tahun']) || !isset($row['jumlah']) || !isset($row['nama_kecamatan']) || !isset($row['nama_desa'])) {
                $this->template = 'Template file tidak sesuai';
                break;
            } else {
                $kecamatan = (strlen($row['kode_kecamatan']) < 2 ? ('0' . $row['kode_kecamatan']) : $row['kode_kecamatan']);
                $check = DB::table('data_dpt')->where('desakel_id', $row['kode_desa'])->where('kec_id', $kecamatan)->get();
                if (count($check) > 0) {
                    // $this->duplicate[] = $row;
                    array_push($this->duplicate, $row);
                } else {
                    DB::table('data_dpt')->insert([
                        'kotakab_id'    => 13,
                        'kec_id'        => $kecamatan,
                        'desakel_id'    => $row['kode_desa'],
                        'full_id'       => 13 . $kecamatan . $row['kode_desa'],
                        'tahun_dpt'     => $row['tahun'],
                        'total'         => $row['jumlah'],
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
