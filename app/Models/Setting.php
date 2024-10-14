<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    use HasFactory;

    public function get_app()
    {
        return DB::table('config_app')->orderBy('id')->first();
    }

    public function update_config($data, $id)
    {
        return DB::table('config_app')->where('id', $id)->update($data);
    }

    public function data_paslon()
    {
        return DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
    }

    public function detail_paslon($uuid)
    {
        return DB::table('data_paslon')->where('uuid_paslon', $uuid)->first();
    }

    public function save_paslon($data)
    {
        return DB::table('data_paslon')->insert($data);
    }

    public function update_paslon($data, $uid)
    {
        return DB::table('data_paslon')->where('uuid_paslon', $uid)->update($data);
    }

    public function delete_paslon($uid)
    {
        $check = DB::table('data_paslon')->where('uuid_paslon', $uid)->first();
        $del   = DB::table('data_paslon')->where('uuid_paslon', $uid)->delete();
        if ($del) {
            return $check->foto_paslon;
        } else {
            return false;
        }
    }
}
