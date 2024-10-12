<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Data;
use App\Models\Setting;
use App\Imports\DptImport;
use App\Exports\TemplateDownload;

class DataController extends Controller
{
    private $data;
    private $setting;
    public function __construct() {
        $this->data = new Data();
        $this->setting = new Setting();
    }

    public function wilayah(Request $request)
    {
        return Inertia::render('Wilayah', [
            'apps'  => $this->setting->get_app(),
            'status' => session('status'),
            'kec'   => $this->data->data_kecamatan(),
            'desa'  => $this->data->data_desa(),
        ]);
    }

    public function dpt(Request $request)
    {
        return Inertia::render('Pemilih', [
            'apps'  => $this->setting->get_app(),
            'status' => session('status'),
            'dpt'   => $this->data->data_dpt(),
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
                $msg = 'Data gagal di simpan atau duplikat data';
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

    public function add_desa(Request $request)
    {
        $validated = $request->validate([
            'code'  => 'required|string',
            'name'  => 'required|string',
            'kec'   => 'required',
            'type'  => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'new') {
                $data = [
                    'full_id'       => '13' . $request->kec . $request->code,
                    'kec_id'        => $request->kec,
                    'desakel_id'    => $request->code,
                    'kotakab_id'    => 13,
                    'desakel_name'  => $request->name,
                ];

                $save = $this->data->add_desa($data);
            } elseif ($request->type == 'edit') {
                $data = [
                    'full_id'       => '13' . $request->kec . $request->code,
                    'kec_id'        => $request->kec,
                    'desakel_id'    => $request->code,
                    'desakel_name'  => $request->name,
                ];

                $save = $this->data->update_desa($data, $request->id);
            }
            if ($save) {
                $status = 'success';
                $msg = 'Data berhasil di simpan';
            } else {
                $status = 'error';
                $msg = 'Data gagal di simpan atau duplikat data';
            }
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('wilayah')->with('message', $res);
    }

    public function delete_desa(Request $request)
    {
        $validated = $request->validate([
            'id'    => 'required',
            'code'  => 'required',
            'type'  => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'single') {
                $del = $this->data->delete_desa($request->id);
            } elseif ($request->type == 'multi') {
                $del = $this->data->delete_desa_multi($request->id);
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

    public function add_dpt(Request $request)
    {
        $validated = $request->validate([
            'kec'   => 'required|string',
            'desa'  => 'required|string',
            'tahun' => 'required|numeric',
            'total' => 'required|numeric',
            'type'  => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'new') {
                $data = [
                    'kotakab_id'    => 13,
                    'kec_id'        => $request->kec,
                    'desakel_id'    => $request->desa,
                    'full_id'       => '13' . $request->kec . $request->desa,
                    'tahun_dpt'     => $request->tahun,
                    'total'         => $request->total,
                    'created_at'    => date('Y-m-d H:i:s')
                ];

                $save = $this->data->add_dpt($data);
            } elseif ($request->type == 'edit') {
                $data = [
                    'kec_id'        => $request->kec,
                    'desakel_id'    => $request->desa,
                    'full_id'       => '13' . $request->kec . $request->desa,
                    'tahun_dpt'     => $request->tahun,
                    'total'         => $request->total,
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $save = $this->data->update_dpt($data, $request->id);
            }
            if ($save) {
                $status = 'success';
                $msg = 'Data berhasil di simpan';
            } else {
                $status = 'error';
                $msg = 'Data gagal di simpan atau duplikat data';
            }
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('dpt')->with('message', $res);
    }

    public function delete_dpt(Request $request)
    {
        $validated = $request->validate([
            'id'    => 'required',
            'type'  => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'single') {
                $del = $this->data->delete_dpt($request->id);
            } elseif ($request->type == 'multi') {
                $del = $this->data->delete_dpt_multi($request->id);
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
        return Redirect::route('dpt')->with('message', $res);
    }

    public function import_dpt(Request $request)
    {
        $validated = $request->validate([
            'import_file'   => 'required|mimes:xlsx',
        ]);

        if ($validated) {
            // Excel::queueImport(new DptImport, $request->file('import_file')->store('temp'));
            $import = new DptImport;
            Excel::import($import, $request->file('import_file'));
            if ($import) {
                if (strlen($import->template) > 0) {
                    $status = 'template';
                    $msg = $import->template;
                    $data = null;
                } else {
                    $status = 'success';
                    $msg = 'Data berhasil di import';
                    $data = $import->duplicate;
                }
            } else {
                $status = 'error';
                $msg = 'Data gagal di import';
                $data = null;
            }

            $res  = ['status' => $status, 'msg' => $msg, 'data' => $data];
            return Redirect::route('dpt')->with('message', $res);
            // return dd($import->duplicate);
        }
    }

    public function template_download($req)
    {
        $types = ['kecamatan', 'desa', 'dpt'];
        // $request->validate([
        //     'type'  => 'required|string'
        // ]);

        // Excel::download(new TemplateDownload($request->type), 'template_' . $request->type . '.xlsx');

        // return Redirect::route('dpt');
        $text = strtolower($req);
        if (in_array($text, $types)) {
            return Excel::download(new TemplateDownload($text), 'template_' . $text . '.xlsx');
        } else {
            return abort(404);
        }
    }

}
