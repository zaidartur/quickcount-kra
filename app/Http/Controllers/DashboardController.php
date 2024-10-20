<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Setting;
use App\Models\Statistik;
use App\Models\Vote;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private $data;
    private $vote;
    private $setting;
    private $statistik;
    public function __construct() {
        $this->data = new Data();
        $this->vote = new Vote();
        $this->setting = new Setting();
        $this->statistik = new Statistik();
    }

    public function view()
    {
        return Inertia::render('Dashboard', [
            'apps'   => $this->setting->get_app(),
            'status' => session('status'),
            'paslon' => $this->setting->data_paslon(),
            'kec'   => $this->data->data_kecamatan(),
            'desa'  => $this->data->data_desa(),
            'alldata' => $this->vote->all_data_voting(),
        ]);
    }
}
