<?php

namespace App\Exports;

use App\Exports\Sheets\HasilSheet;
use App\Exports\Sheets\KecamatanSheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataSuara implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        $kecamatan = DB::table('data_kecamatan')->orderBy('id', 'asc')->get();

        $sheets[0] = new HasilSheet();
        $no = 1;
        foreach($kecamatan as $kec) {
            $sheets[$no] = new KecamatanSheet($kec->kec_id, $kec->kec_name);
            $no++;
        }

        return $sheets;
    }
}
