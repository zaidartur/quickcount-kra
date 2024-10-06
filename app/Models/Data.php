<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Data extends Model
{
    use HasFactory;

    public function data_kecamatan()
    {
        return DB::table('data_kecamatan')->get();
    }

    public function data_desa()
    {
        return DB::table('data_desa as dd')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dd.kec_id')
                ->select('dd.desakel_id', 'dd.kec_id', 'dd.desakel_name', 'dk.kec_name')
                ->orderBy('dd.desakel_id')->get();
    }

    public function add_kecamatan($data)
    {
        if (DB::table('data_kecamatan')->where('kec_id', $data['kec_id'])->exists()) {
            return false;
        } else {
            return DB::table('data_kecamatan')->insert($data);
            // return true;
        }
    }

    public function update_kecamatan($data, $id)
    {
        $check = DB::table('data_kecamatan')->where('kec_id', $data['kec_id'])->whereNot('id', $id)->get();
        if (count($check) > 0) {
            return false;
        } else {
            return DB::table('data_kecamatan')->where('id', $id)->update($data);
        }
    }
    
    public function delete_kecamatan($id)
    {
        return DB::table('data_kecamatan')->where('id', $id)->delete();
    }

    public function delete_kecamatan_multi($id)
    {
        $uid = explode(',', $id);
        return DB::table('data_kecamatan')->whereIn('id', $uid)->delete();
    }

    public function add_desa($data)
    {
        return DB::table('data_desa')->insert($data);
    }

    public function update_desa($data, $id)
    {
        return DB::table('data_desa')->where('id', $id)->update($data);
    }
}
