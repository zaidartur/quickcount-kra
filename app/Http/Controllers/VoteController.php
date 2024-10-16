<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Vote;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VoteController extends Controller
{
    private $vote;
    private $setting;
    public function __construct() {
        $this->vote = new Vote();
        $this->setting = new Setting();
    }

    public function view()
    {
        if (Auth::user()->level == 2 || Auth::user()->level == 3) {
            return Inertia::render('Voting', [
                'apps'   => $this->setting->get_app(),
                'status' => session('status'),
                'paslon' => $this->setting->data_paslon(),
            ]);
        } else {
            return abort(404);
        }
    }
}
