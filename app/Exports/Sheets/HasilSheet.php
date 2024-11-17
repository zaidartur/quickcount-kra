<?php

namespace App\Exports\Sheets;

use App\Models\Setting;
use App\Models\Statistik;
use App\Models\Vote;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class HasilSheet implements FromView, WithTitle
{
    private $vote;
    private $setting;
    private $statistik;
    public function __construct() {
        $this->vote = new Vote();
        $this->setting = new Setting();
        $this->statistik = new Statistik();
    }
    
    public function view(): View
    {
        $data = [
            'data'      => $this->statistik->statistik_kecamatan(),
            'paslon'    => $this->setting->data_paslon(),
            'alldata'   => $this->vote->all_data_voting(),
        ];
        return view('template_hasil_suara', $data);
    }

    public function title(): string
    {
        return 'Rekapitulasi';
    }
}
