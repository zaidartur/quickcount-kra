<?php

namespace App\Http\Controllers;

use App\Exports\DataSuara;
use App\Models\Data;
use App\Models\Setting;
use App\Models\Statistik;
use App\Models\Vote;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class StatistikController extends Controller
{
    private $vote;
    private $data;
    private $setting;
    private $statistik;
    public function __construct() {
        $this->vote = new Vote();
        $this->data = new Data();
        $this->setting = new Setting();
        $this->statistik = new Statistik();
    }

    public function view()
    {
        return Inertia::render('Statistik', [
            'apps'      => $this->setting->get_app(),
            'status'    => session('status'),
            'paslon'    => $this->setting->data_paslon(),
            'kec'       => $this->data->data_kecamatan(),
            'desa'      => $this->data->data_desa(),
            'statkec'   => $this->statistik->statistik_kecamatan(),
        ]);
    }

    public function view_tabel()
    {
        return Inertia::render('Tabel', [
            'apps'      => $this->setting->get_app(),
            'status'    => session('status'),
            'paslon'    => $this->setting->data_paslon(),
            'kec'       => $this->data->data_kecamatan(),
            'desa'      => $this->data->data_desa(),
            'statkec'   => $this->statistik->statistik_kecamatan(),
        ]);
    }

    public function detail_statistik($uid)
    {
        //
    }

    public function get_statistik_kecamatan()
    {
        return $this->statistik->statistik_kecamatan();
    }

    public function detail_kecamatan(Request $request)
    {
        if (!empty($request->kecamatan)) {
            return $this->statistik->statistik_desa($request->kecamatan);
        } else {
            return [];
        }
    }

    public function export_statistik()
    {
        return Excel::download(new DataSuara(), 'data_suara_' . date('YmdHis') . '.xlsx');
    }

    public function testing()
    {
        // return $this->statistik->statistik_kecamatan();
        return $this->vote->grafik();
    }

    public function testing_desa($kec)
    {
        return $this->statistik->statistik_desa($kec);
    }

    public function testing_tabel()
    {
        $data = [
            'data'      => $this->statistik->statistik_kecamatan(),
            'paslon'    => $this->setting->data_paslon(),
            'alldata'   => $this->vote->all_data_voting(),
        ];
        return view('template_hasil_suara', $data);
    }

    public function testing_desa_tabel($kec)
    {
        $data = [
            'data'      => $this->statistik->statistik_desa($kec),
            'paslon'    => $this->setting->data_paslon(),
            'alldata'   => $this->vote->data_voting_paslon_kecamatan($kec),
            'kecamatan' => $this->data->detail_kecamatan($kec),
            'tps'       => $this->statistik->statistik_tps($kec),
        ];
        return view('template_suara_kecamatan', $data);
    }
}
