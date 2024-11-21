<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

use App\Models\Setting;

class SettingController extends Controller
{
    private $setting;
    public function __construct() {
        $this->setting = new Setting();
    }

    public function view()
    {
        if (Auth::user()->level < 2) {
            return Inertia::render('Setting', [
                'apps'   => $this->setting->get_app(),
                'status' => session('status'),
                'paslon' => $this->setting->data_paslon(),
            ]);
        } else {
            return abort(404);
        }
    }

    public function update_config(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string',
            'pemilik'   => 'required|string',
            'tahun'     => 'required|numeric',
        ]);

        if ($validated) {
            $check = $this->setting->get_app();
            if (isset($request->logo)) {
                if (File::exists(public_path('/uploads/app'))) {
                    File::makeDirectory(public_path('/uploads/app'), 0777, true, true);
                }
                if (!empty($check->app_logo)) {
                    $old = public_path() . $check->app_logo;
                    if (File::exists($old)) {
                        File::delete($old);
                    }
                }
                $path = public_path('/uploads/app');
                $logo = 'logo.'.$request->logo->extension();  
                $request->logo->move($path, $logo);
            } else {
                $logo = null;
            }
            $data = [
                'app_name'      => $request->name,
                'app_pemilik'   => $request->pemilik,
                'app_tahun'     => $request->tahun,
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            if (isset($request->website)) {
                $data += ['app_website'   => $request->website,];
            }
            if (isset($request->alamat)) {
                $data += ['app_address'   => $request->alamat,];
            }
            if (!empty($logo)) {
                $data += ['app_logo'    => '/uploads/app/' . $logo,];
            }

            $save = $this->setting->update_config($data, $request->id);
            if ($save) {
                $status = 'success';
                $msg = 'Data berhasil di update';
            } else {
                $status = 'error';
                $msg = 'Data gagal di update';
            }
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('setting')->with('message', $res);
    }

    public function add_paslon(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string',
            'urut'  => 'required',
            'tahun' => 'required|numeric',
            // 'foto'  => 'required|image|mimes:png,jpg,jpeg,gif|max:2048',
            'type'  => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'new') {
                $save = $this->save_paslon($request);
            } elseif ($request->type == 'edit') {
                $save = $this->update_paslon($request);
            }
            $status = $save['status'];
            $msg = $save['msg'];
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('setting')->with('message', $res);
    }

    public function save_paslon($request)
    {
        $uuid = Uuid::uuid4()->toString();
            
        if (isset($request->foto)) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows Path
                $upload_path = public_path() . '\uploads\paslon';
            } else {
                $upload_path = public_path() . '/uploads/paslon';
            }
            
            if (!File::exists($upload_path)) {
                File::makeDirectory($upload_path, 0777, true, true);
            }
            $foto = $uuid.'.'.$request->foto->extension();  
            $request->foto->move($upload_path, $foto);
        } else {
            $foto = null;
        }

        $data = [
            'uuid_paslon'   => $uuid,
            'no_urut'       => $request->urut,
            'nama_paslon'   => $request->name,
            'tahun'         => $request->tahun,
            'created_at'    => date('Y-m-d H:i:s'),
        ];
        if (!empty($foto)) {
            $data += [
                'foto_paslon'   => '/uploads/paslon/' . $foto,
            ];
        }

        $save = $this->setting->save_paslon($data);
        if ($save) {
            $status = 'success';
            $msg = 'Data berhasil di update';
        } else {
            $loc = public_path('') . '/uploads/paslon/' . $foto;
            if (File::exists($loc)) {
                File::delete($loc);
            }
            $status = 'error';
            $msg = 'Data gagal di update';
        }

        return ['status' => $status, 'msg' => $msg];
    }

    public function update_paslon($request)
    {
        $check = $this->setting->detail_paslon($request->id);

        // check foto
        if ($request->foto_status == 'new') {
            if (File::exists(public_path($check->foto_paslon))) {
                File::delete(public_path($check->foto_paslon));
            }
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $upload_path = public_path() . '\uploads\paslon';
            } else {
                $upload_path = public_path() . '/uploads/paslon';
            }
            // $upload_path = public_path() . '/uploads/paslon';
            if (!File::exists($upload_path)) {
                File::makeDirectory($upload_path, 0777, true, true);
            }
            $foto = $check->uuid_paslon.'.'.$request->foto->extension();  
            $request->foto->move($upload_path, $foto);
        }

        $data = [
            'no_urut'       => $request->urut,
            'nama_paslon'   => $request->name,
            'tahun'         => $request->tahun,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        if ($request->foto_status == 'new') {
            $data += [
                'foto_paslon'   => '/uploads/paslon/' . $foto,
            ];
        }

        $save = $this->setting->update_paslon($data, $request->id);
        if ($save) {
            $status = 'success';
            $msg = 'Data berhasil di update';
        } else {
            $status = 'error';
            $msg = 'Data gagal di update';
        }

        return ['status' => $status, 'msg' => $msg];
    }

    public function delete_paslon(Request $request)
    {
        $validated = $request->validate([
            'id'    => 'required|string'
        ]);

        if ($validated) {
            $del = $this->setting->delete_paslon($request->id);
            if ($del && File::exists(public_path($del))) {
                File::delete(public_path($del));
            }
            $status = 'success';
            $msg = 'Data berhasil di hapus';
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('setting')->with('message', $res);
    }
}
