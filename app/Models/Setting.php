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

    public function data_paslon()
    {
        return DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
    }
}
