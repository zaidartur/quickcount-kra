<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

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

    public function check_email($mail)
    {
        $check = User::where('email', $mail)->get();
        if (count($check) > 0) {
            return json_encode('false');
        } else {
            return json_encode('true');
        }
    }

    public function add_user(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string',
            'email' => 'required|email',
            'level' => 'required',
            'kode'  => 'required',
            'type'  => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'new') {
                $data = [
                    'name'      => $request->name,
                    'uuid'      => Uuid::uuid4()->toString(),
                    'email'     => $request->email,
                    'password'  => Hash::make($request->pass),
                    'level'     => $request->level,
                    'kode'      => $request->kode,
                ];
                $save = User::create($data);
            } elseif ($request->type == 'edit') {
                $data = [
                    'name'      => $request->name,
                    'level'     => $request->level,
                    'kode'      => $request->kode,
                ];
                $save = User::where('uuid', $request->uuid)->update($data);
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
        return Redirect::route('users')->with('message', $res);
    }

    public function detail_user($uid)
    {
        if (Auth::user()->level < 2) {
            $cek = User::where('uuid', $uid)->first();
            if ($cek) {
                $res = $this->users->session_user($cek->id);
                return ['status' => '200', 'data' => $res];
            } else {
                return ['status' => '404', 'data' => []];
            }
        } else {
            return ['status' => '401', 'data' => []];
        }
    }

    public function change_pwd_user(Request $request)
    {
        $validated = $request->validate([
            'uuid'  => 'required|string',
            'pass'  => 'required|string',
        ]);

        if ($validated) {
            $data = [
                'password'  => Hash::make($request->pass),
                'updated_at'=> date('Y-m-d H:i:s'),
            ];
            $save = User::where('uuid', $request->uuid)->update($data);
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
        return Redirect::route('users')->with('message', $res);
    }

    public function delete_user(Request $request)
    {
        $validated = $request->validate([
            'uuid'  => 'required|string',
            'pass'  => 'required|string',
        ]);

        if ($validated) {
            $cek  = $this->users->detail_user(Auth::user()->uuid);
            if (Hash::check($request->pass, $cek->password)) {
                $drop = User::where('uuid', $request->uuid)->delete();
                if ($drop) {
                    $status = 'success';
                    $msg = 'Data berhasil di hapus';
                } else {
                    $status = 'error';
                    $msg = 'Data gagal di hapus';
                }
            } else {
                $status = 'error';
                $msg = 'Password tidak sesuai';
            }
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('users')->with('message', $res);
    }
}

