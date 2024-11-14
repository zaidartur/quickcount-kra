<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'uuid',
        'email',
        'password',
        'level',
        'kode'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function detail_user($uid)
    {
        return DB::table('users')->where('uuid', $uid)->select('*')->first();
    }

    public function update_user($data, $id) 
    {
        return DB::table('users')->where('uuid', $id)->update($data);
    }

    public function role_profile()
    {
        $level = Auth::user()->level;
        if ($level == 0) {
            return ['title' => 'Super Admin', 'text' => ''];
        } elseif ($level == 1) {
            return ['title' => 'Admin Kabupaten', 'text' => ''];
        } elseif ($level == 2) {
            $kode = (strlen(Auth::user()->kode) < 2 ? ('0'.Auth::user()->kode) : Auth::user()->kode);
            $kec  = DB::table('data_kecamatan')->where('kec_id', $kode)->first();
            return ['title' => 'Admin Kecamatan', 'text' => $kec->kec_name];
        } elseif ($level == 3) {
            $desa = DB::table('data_desa as dd')
                    ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dd.kec_id')
                    ->where('dd.full_id', Auth::user()->kode)
                    ->select('dd.*', 'dk.kec_name')
                    ->first();
            return ['title' => 'Admin Desa/Kelurahan', 'text' => $desa->desakel_name.', '.$desa->kec_name];
        } elseif ($level == 4) {
            $kode = explode('-', Auth::user()->kode);
            $tps = DB::table('data_dpt as dpt')
                    ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dpt.full_id')
                    ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dpt.kec_id')
                    ->select('dpt.no_tps', 'dd.desakel_name', 'dk.kec_name')
                    ->where('dpt.full_id', $kode[0])->where('dpt.id', $kode[1])
                    ->first();
            $tps_name = (intval($tps->no_tps) < 10 ? ('00'.$tps->no_tps) : ((intval($tps->no_tps) > 9 && intval($tps->no_tps < 100)) ? ('0'.$tps->no_tps) : $tps->no_tps));
            return ['title' => 'Admin TPS', 'text' => $tps_name.', '.$tps->desakel_name.', '.$tps->kec_name];
        }
    }

    public function list_user()
    {
        $level = Auth::user()->level;
        if ($level == 0) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->leftJoin(DB::raw('(SELECT data_dpt.no_tps, CONCAT(data_dpt.full_id,"-",data_dpt.id) AS code FROM data_dpt) as dpt'), 'dpt.code', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name', 'dpt.no_tps')
                ->orderBy('u.level')->orderBy('u.name')->get();
        } elseif ($level == 1) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->leftJoin(DB::raw('(SELECT data_dpt.no_tps, CONCAT(data_dpt.full_id,"-",data_dpt.id) AS code FROM data_dpt) as dpt'), 'dpt.code', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name', 'dpt.no_tps')
                ->whereNot('u.level', 0)
                ->orderBy('u.level')->orderBy('u.name')->get();
        } elseif ($level == 2) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->leftJoin(DB::raw('(SELECT data_dpt.no_tps, CONCAT(data_dpt.full_id,"-",data_dpt.id) AS code FROM data_dpt) as dpt'), 'dpt.code', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name', 'dpt.no_tps')
                ->whereIn('u.level', [2,3,4])
                ->where('u.kode', Auth::user()->kode)->orWhereRaw('SUBSTR(u.kode,3,2) = ' . Auth::user()->kode)
                // ->orWhere('kode', 'like', '%'.Auth::user()->kode.'%')
                ->orderBy('u.level')->orderBy('u.name')->get();
        } elseif ($level == 3) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->leftJoin(DB::raw('(SELECT data_dpt.no_tps, CONCAT(data_dpt.full_id,"-",data_dpt.id) AS code FROM data_dpt) as dpt'), 'dpt.code', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name', 'dpt.no_tps')
                // ->where('u.level', 3)->where('kode', Auth::user()->kode)
                ->whereIn('u.level', [3,4])->where('u.kode', 'like', Auth::user()->kode . '%')
                ->orderBy('u.level')->orderBy('u.name')->get();
        } elseif ($level == 4) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->leftJoin(DB::raw('(SELECT data_dpt.no_tps, CONCAT(data_dpt.full_id,"-",data_dpt.id) AS code FROM data_dpt) as dpt'), 'dpt.code', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name', 'dpt.no_tps')
                ->where('u.level', 4)->where('kode', Auth::user()->kode)
                ->orderBy('u.level')->orderBy('u.name')->get();
        }
    }

    public function session_user($uid)
    {
        return DB::table('sessions')->where('user_id', $uid)->get();
    }

}
