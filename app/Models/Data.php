<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\returnValueMap;

class Data extends Model
{
    use HasFactory;

    public function data_kecamatan()
    {
        return DB::table('data_kecamatan')->get();
    }

    public function detail_kecamatan($kec)
    {
        return DB::table('data_kecamatan')->where('kec_id', $kec)->first();
    }

    public function all_desa()
    {
        return DB::table('data_desa as dd')
            ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dd.kec_id')
            ->select('dd.id', 'dd.desakel_id', 'dd.kec_id', 'dd.desakel_name', 'dd.full_id', 'dk.kec_name')
            ->orderBy('dd.desakel_id')->get();
    }

    public function data_desa()
    {
        $level = Auth::user()->level;
        $data  = DB::table('data_desa as dd')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dd.kec_id')
                ->select('dd.id', 'dd.desakel_id', 'dd.kec_id', 'dd.desakel_name', 'dd.full_id', 'dk.kec_name');
        if ($level == 2) {
            $data->where('dd.kec_id', Auth::user()->kode);
        }
        return $data->orderBy('dd.desakel_id')->get();
    }

    public function data_tps_desa($desa)
    {
        return DB::table('data_dpt')->where('full_id', $desa)->get();
    }

    public function sum_dpt()
    {
        $level = Auth::user()->level;
        $data  = DB::table('data_dpt as dpt');
        if ($level == 2) {
            $data->where('dpt.kec_id', Auth::user()->kode);
        }
        if ($level == 3) {
            $data->where('dpt.full_id', Auth::user()->kode);
            $data->orderBy('dpt.no_tps', 'asc');
        }
        if ($level == 4) {
            $kode = explode('-', Auth::user()->kode);
            $data->where('dpt.id', $kode[1]);
            $data->orderBy('dpt.no_tps', 'asc');
        }
        return $data->sum('total');
    }

    public function data_dpt()
    {
        $level = Auth::user()->level;
        $data  = DB::table('data_dpt as dpt')
            ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dpt.kec_id')
            ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dpt.full_id')
            ->select('dpt.*', 'dk.kec_name', 'dd.desakel_name');
        if ($level == 2) {
            $data->where('dpt.kec_id', Auth::user()->kode);
        }
        if ($level == 3) {
            $data->where('dpt.full_id', Auth::user()->kode);
            $data->orderBy('dpt.no_tps', 'asc');
        }
        if ($level == 4) {
            $kode = explode('-', Auth::user()->kode);
            $data->where('dpt.id', $kode[1]);
            $data->orderBy('dpt.no_tps', 'asc');
        }
        return $data->get();
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
        if (DB::table('data_desa')->where('desakel_id', $data['desakel_id'])->where('kec_id', $data['kec_id'])->exists()) {
            return false;
        } else {
            return DB::table('data_desa')->insert($data);
        }
    }

    public function update_desa($data, $id)
    {
        $check = DB::table('data_desa')->where('desakel_id', $data['desakel_id'])->where('kec_id', $data['kec_id'])->whereNot('id', $id)->get();
        if (count($check) > 0) {
            return false;
        } else {
            return DB::table('data_desa')->where('id', $id)->update($data);
        }
    }

    public function delete_desa($id)
    {
        return DB::table('data_desa')->where('id', $id)->delete();
    }

    public function delete_desa_multi($id)
    {
        $uid = explode(',', $id);
        return DB::table('data_desa')->whereIn('id', $uid)->delete();
    }

    public function add_dpt($data)
    {
        if (DB::table('data_dpt')->where('desakel_id', $data['desakel_id'])->where('kec_id', $data['kec_id'])->exists()) {
            return false;
        } else {
            return DB::table('data_dpt')->insert($data);
        }
    }

    public function update_dpt($data, $id)
    {
        $check = DB::table('data_dpt')->where('full_id', $data['full_id'])->whereNot('id', $id)->get();
        if (count($check) > 0) {
            return false;
        } else {
            return DB::table('data_dpt')->where('id', $id)->update($data);
        }
    }

    public function delete_dpt($id)
    {
        return DB::table('data_dpt')->where('id', $id)->delete();
    }

    public function delete_dpt_multi($id)
    {
        $uid = explode(',', $id);
        return DB::table('data_dpt')->whereIn('id', $uid)->delete();
    }
}
