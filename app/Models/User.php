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

    public function list_user()
    {
        $level = Auth::user()->level;
        if ($level == 0) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name')
                ->orderBy('u.level')->orderBy('u.name')->get();
        } elseif ($level == 1) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name')
                ->whereNot('u.level', 0)
                ->orderBy('u.level')->orderBy('u.name')->get();
        } elseif ($level == 2) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name')
                ->whereIn('u.level', [2,3])
                ->where('kode', Auth::user()->kode)->orWhereRaw('SUBSTR(kode,3,2) = ' . Auth::user()->kode)
                // ->orWhere('kode', 'like', '%'.Auth::user()->kode.'%')
                ->orderBy('u.level')->orderBy('u.name')->get();
        } elseif ($level == 3) {
            return DB::table('users as u')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name')
                ->where('u.level', 3)->where('kode', Auth::user()->kode)
                ->orderBy('u.level')->orderBy('u.name')->get();
        }
    }

    public function session_user($uid)
    {
        return DB::table('sessions')->where('user_id', $uid)->get();
    }

}
