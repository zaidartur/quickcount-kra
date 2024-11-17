<?php

namespace App\Exports\Sheets;

use App\Models\Data;
use App\Models\Setting;
use App\Models\Statistik;
use App\Models\Vote;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class KecamatanSheet implements FromView, WithTitle
{
    private $kec;
    private $name;
    private $vote;
    private $data;
    private $setting;
    private $statistik;
    public function __construct($kec, $name)
    {
        $this->kec = $kec;
        $this->name = $name;
        $this->vote = new Vote();
        $this->data = new Data();
        $this->setting = new Setting();
        $this->statistik = new Statistik();
    }

    public function view(): View
    {
        $data = [
            'data'      => $this->statistik->statistik_desa($this->kec),
            'paslon'    => $this->setting->data_paslon(),
            'alldata'   => $this->vote->data_voting_paslon_kecamatan($this->kec),
            'kecamatan' => $this->data->detail_kecamatan($this->kec),
            'tps'       => $this->statistik->statistik_tps($this->kec),
        ];
        return view('template_suara_kecamatan', $data);
    }

    public function title(): string
    {
        return $this->name;
    }
}
