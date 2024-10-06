<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

use App\Models\Data;

class DataController extends Controller
{
    private $data;
    public function __construct() {
        $this->data = new Data();
    }

    public function wilayah(Request $request)
    {
        return Inertia::render('Wilayah', [
            'status' => session('status'),
            'kec'   => $this->data->data_kecamatan(),
            'desa'  => $this->data->data_desa(),
        ]);
    }

    public function list_kecamatan(Request $request)
    {
        return $this->data->data_kecamatan();
    }

    public function add_kecamatan(Request $request)
    {
        $validated = $request->validate([
            'code'    => 'required|numeric',
            'name'    => 'required|string',
            'type'    => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'new') {
                $data = [
                    'kec_id'    => $request->code,
                    'kotakab_id'=> '33',
                    'kec_name'  => $request->name,
                ];

                $save = $this->data->add_kecamatan($data);
            } elseif ($request->type == 'edit') {
                $data = [
                    'kec_id'    => $request->code,
                    'kec_name'  => $request->name,
                ];

                $save = $this->data->update_kecamatan($data, $request->id);
            }
            if ($save) {
                $status = 'success';
                $msg = 'Data berhasil di simpan';
            } else {
                $status = 'error';
                $msg = 'Data gagal di simpan';
            }
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }
        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('wilayah')->with('message', $res);
    }

    public function delete_kecamatan(Request $request)
    {
        $validated = $request->validate([
            'id'    => 'required',
            'code'  => 'required',
            'type'  => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'single') {
                $del = $this->data->delete_kecamatan($request->id);
            } elseif ($request->type == 'multi') {
                $del = $this->data->delete_kecamatan_multi($request->id);
            }
            if ($del) {
                $status = 'success';
                $msg = 'Data berhasil di hapus';
            } else {
                $status = 'error';
                $msg = 'Data gagal di hapus';
            }
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('wilayah')->with('message', $res);
    }

}
