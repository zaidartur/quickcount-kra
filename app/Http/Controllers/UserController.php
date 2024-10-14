<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\User;
use App\Models\Data;
use App\Models\Setting;

class UserController extends Controller
{
    private $users;
    private $setting;
    private $data;
    public function __construct() {
        $this->users = new User();
        $this->setting = new Setting();
        $this->data = new Data();
    }

    public function view()
    {
        return Inertia::render('DataUsers', [
            'apps'   => $this->setting->get_app(),
            'status' => session('status'),
            'users' => $this->users->list_user(),
            'kec'   => $this->data->data_kecamatan(),
            'desa'  => $this->data->data_desa(),
        ]);
    }
}

