<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Vote extends Model
{
    use HasFactory;

    public function data_voting()
    {
        $level = Auth::user()->level;
        if ($level == 2) {
            $res = DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                    ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                    ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                    ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.desakel_id', 'u.name', 'u.level', 'u.kode')
                    ->where('dv.kec_id', Auth::user()->kode)
                    ->get();
        } elseif ($level == 3) {
            $res = DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                    ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                    ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                    ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.desakel_id', 'u.name', 'u.level', 'u.kode')
                    ->where('dv.desakel_id', Auth::user()->kode)
                    ->get(); 
        } else {
            $res = [];
        }

        return $res;
    }

    public function all_data_voting()
    {
        return DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.desakel_id', 'u.name', 'u.level', 'u.kode')
                ->get();
    }

    public function save_voting($data)
    {
        return DB::table('data_voting')->insert($data);
    }

    public function update_voting($data, $id)
    {
        return DB::table('data_voting')->where('uuid_vote', $id)->update($data);
    }
}
