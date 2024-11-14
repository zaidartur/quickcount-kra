<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vote;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Hash;

class VoteController extends Controller
{
    private $vote;
    private $data;
    private $user;
    private $setting;
    public function __construct() {
        $this->vote = new Vote();
        $this->data = new Data();
        $this->user = new User();
        $this->setting = new Setting();
    }

    public function view()
    {
        if (in_array(Auth::user()->level, [2,3,4])) {
            return Inertia::render('Voting', [
                'apps'   => $this->setting->get_app(),
                'status' => session('status'),
                'paslon' => $this->setting->data_paslon(),
                'kec'    => $this->data->data_kecamatan(),
                'desa'   => $this->data->data_desa(),
                'mydata' => $this->vote->data_voting(),
                'profile'=> $this->user->role_profile(),
                'dpt'    => $this->data->sum_dpt(),
            ]);
        } else {
            return abort(404);
        }
    }

    public function add_voting(Request $request)
    {
        $validated = $request->validate([
            'dpt'       => 'required',
            'kec'       => 'required',
            'desa'      => 'required',
            'desaName'  => 'required|string',
            'tps'       => 'required',
            'voteValid' => 'required',
            // 'voteInvalid' => 'required',
            'totalVote' => 'required',
            'user'      => 'required|string',
            'type'      => 'required|string',
        ]);

        if ($validated) {
            if ($request->type == 'new') {
                $save = $this->save_voting($request);
            } elseif ($request->type == 'update') {
                $save = $this->update_voting($request);
            }
            $status = $save['status'];
            $msg = $save['msg'];
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('vote')->with('message', $res);
    }

    public function save_voting($request)
    {
        $cek  = $this->vote->check_data($request->desa, $request->dpt);
        if (count($cek) > 0) {
            $status = 'error';
            $msg = 'Duplikat data';
        } else {
            $uuid = Uuid::uuid4()->toString();
            $data = [
                'uuid_vote'     => $uuid,
                'dpt_id'        => $request->dpt,
                'kec_id'        => (strlen($request->kec) < 2 ? ('0'.$request->kec) : $request->kec),
                'desakel_id'    => $request->desa,
                'desakel_name'  => $request->desaName,
                'no_tps'        => intval($request->tps),
                'vote_sah'      => $request->voteValid,
                'vote_tidaksah' => intval($request->voteInvalid) ?? 0,
                'tahun_vote'    => date('Y'),
                'total_vote'    => intval($request->totalVote),
                'user'          => $request->user,
                'created_at'    => date('Y-m-d H:i:s'),
            ];

            $save = $this->vote->save_voting($data);
            if ($save) {
                $status = 'success';
                $msg = 'Data berhasil di simpan';
            } else {
                $status = 'error';
                $msg = 'Data gagal di simpan';
            }
        }

        return ['status' => $status, 'msg' => $msg];
    }

    public function update_voting($request)
    {
        $data = [
            'kec_id'        => $request->kec,
            'desakel_id'    => $request->desa,
            'desakel_name'  => $request->desaName,
            'vote_sah'      => $request->voteValid,
            'vote_tidaksah' => $request->voteInvalid ?? 0,
            'tahun_vote'    => date('Y'),
            'total_vote'    => $request->totalVote,
            'user'          => $request->user,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $save = $this->vote->update_voting($data, $request->uuid);
        if ($save) {
            $status = 'success';
            $msg = 'Data berhasil di update';
        } else {
            $status = 'error';
            $msg = 'Data gagal di update';
        }

        return ['status' => $status, 'msg' => $msg];
    }

    public function check_password($pwd)
    {
        $cek  = $this->user->detail_user(Auth::user()->uuid);
        $pass = base64_decode($pwd);
        if (Hash::check($pass, $cek->password)) {
            return ['status' => 200];
        } else {
            return ['status' => 401];
        }
    }
}
