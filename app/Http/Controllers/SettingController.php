<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Setting;
use Ramsey\Uuid\Uuid;

class SettingController extends Controller
{
    private $setting;
    public function __construct() {
        $this->setting = new Setting();
    }

    public function view()
    {
        return Inertia::render('Setting', [
            'apps'   => $this->setting->get_app(),
            'status' => session('status'),
            'paslon' => $this->setting->data_paslon(),
        ]);
    }

    public function update_config(Request $request)
    {
        //
    }

    public function add_paslon(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string',
            'urut'  => 'required',
            'tahun' => 'required|numeric',
            'foto'  => 'required|image|mimes:png,jpg,jpeg,gif|max:2048',
        ]);

        if ($validated) {
            $uuid = Uuid::uuid4()->toString();
            // $foto
            $data = [
                'uuid_paslon'   => $uuid,
                'no_urut'       => $request->urut,
                'nama_paslon'   => $request->name,
                // 'foto_paslon'   => $foto,
                'tahun'         => $request->tahun,
                'created_at'    => date('Y-m-d H:i:s'),
            ];
        }
    }
}
