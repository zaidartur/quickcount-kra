<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Setting;
use App\Models\Vote;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VoteController extends Controller
{
    private $vote;
    private $data;
    private $setting;
    public function __construct() {
        $this->vote = new Vote();
        $this->data = new Data();
        $this->setting = new Setting();
    }

    public function view()
    {
        if (Auth::user()->level == 2 || Auth::user()->level == 3) {
            return Inertia::render('Voting', [
                'apps'   => $this->setting->get_app(),
                'status' => session('status'),
                'paslon' => $this->setting->data_paslon(),
                'kec'    => $this->data->data_kecamatan(),
                'desa'   => $this->data->data_desa(),
            ]);
        } else {
            return abort(404);
        }
    }
}