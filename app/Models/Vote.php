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
            $res = $this->data_voting_kecamatan();
        } elseif ($level == 3) {
            $res = DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                    ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                    ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                    ->leftJoin('data_dpt as dpt', 'dpt.id', '=', 'dv.dpt_id')
                    ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.full_id', 'u.name', 'u.level', 'u.kode', 'dpt.no_tps', 'dpt.total')
                    ->where('dv.desakel_id', Auth::user()->kode)
                    ->orderBy('dpt.no_tps')
                    ->get(); 
        } elseif ($level == 4) {
            $kode = explode('-', Auth::user()->kode);
            $res = DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                    ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                    ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                    ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.full_id', 'u.name', 'u.level', 'u.kode')
                    ->where('dv.desakel_id', $kode[0])->where('dpt_id', intval($kode[1]))
                    ->get(); 
        } else {
            $res = [];
        }

        return $res;
    }

    public function data_voting_kecamatan()
    {
        $res   = [];
        $datas = DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                    ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                    ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                    ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.full_id', 'u.name', 'u.level', 'u.kode')
                    ->where('dv.kec_id', Auth::user()->kode)
                    ->get();
        foreach ($datas as $d => $data) {
            $res[] = [
                'id'            => $data->id,
                'uuid_vote'     => $data->uuid_vote,
                'tahun_vote'    => $data->tahun_vote,
                'kec_id'        => $data->kec_id,
                'kec_name'      => $data->kec_name,
                'desakel_id'    => $data->desakel_id,
                'desakel_name'  => $data->desakel_name,
                'valid'         => json_decode($data->vote_sah),
                'invalid'       => $data->vote_tidaksah,
                'total'         => $data->total_vote,
                'user'          => $data->name,
            ];
        }

        return $res;
    }

    public function all_data_voting()
    {
        $result = [];
        $paslon = DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
        $datas  = DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.desakel_id', 'u.name', 'u.level', 'u.kode')
                ->get();

        $invalid = 0;
        foreach ($paslon as $key => $value) {
            $result[$key] = [
                'uuid'  => $value->uuid_paslon,
                'name'  => $value->nama_paslon,
                'urut'  => $value->no_urut,
                'foto'  => $value->foto_paslon,
            ];
            $valid = 0;
            foreach ($datas as $d => $data) {
                $votes = json_decode($data->vote_sah);
                foreach ($votes as $v => $vote) {
                    if ($vote->uuid == $value->uuid_paslon) {
                        $valid = $valid + intval($vote->point);
                    }
                }
            }
            $result[$key] += [
                'total' => $valid,
            ];
        }
        foreach ($datas as $d => $data) {
            $invalid = $invalid + intval($data->vote_tidaksah);
        }

        return ['valid' => $result, 'invalid' => $invalid];
    }

    public function data_voting_paslon_kecamatan($kec)
    {
        $result = [];
        $paslon = DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
        $datas  = DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.desakel_id', 'u.name', 'u.level', 'u.kode')
                ->where('dk.kec_id', $kec)
                ->get();

        $invalid = 0;
        foreach ($paslon as $key => $value) {
            $result[$key] = [
                'uuid'  => $value->uuid_paslon,
                'name'  => $value->nama_paslon,
                'urut'  => $value->no_urut,
                'foto'  => $value->foto_paslon,
            ];
            $valid = 0;
            foreach ($datas as $d => $data) {
                $votes = json_decode($data->vote_sah);
                foreach ($votes as $v => $vote) {
                    if ($vote->uuid == $value->uuid_paslon) {
                        $valid = $valid + intval($vote->point);
                    }
                }
            }
            $result[$key] += [
                'total' => $valid,
            ];
        }
        foreach ($datas as $d => $data) {
            $invalid = $invalid + intval($data->vote_tidaksah);
        }

        return ['valid' => $result, 'invalid' => $invalid];
    }

    public function check_data($desa, $dpt)
    {
        return DB::table('data_voting')->where('desakel_id', $desa)->where('dpt_id', intval($dpt))->get();
    }

    public function save_voting($data)
    {
        return DB::table('data_voting')->insert($data);
    }

    public function update_voting($data, $id)
    {
        return DB::table('data_voting')->where('uuid_vote', $id)->update($data);
    }

    public function grafik()
    {
        $datas  = DB::table('data_voting as dv')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dv.kec_id')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dv.desakel_id')
                ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                ->select('dv.*', 'dk.kec_name', 'dd.desakel_name', 'dd.desakel_id', 'u.name', 'u.level', 'u.kode')
                ->get();
        $data_kec = DB::table('data_kecamatan')->orderBy('kec_id')->get();
        $paslon = DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
        $res  = [];

        // paslon
        foreach ($paslon as $key => $value) {
            $res[$key] = [
                'name'  => $value->nama_paslon,
                'urut'  => $value->no_urut,
                'uuid'  => $value->uuid_paslon,
            ];

            $sah = [];
            $tidaksah = [];
            // kecamatan
            foreach ($data_kec as $dk => $dkec) {
                $valid = 0;
                $invalid = 0;
                // vote
                foreach ($datas as $d => $data) {
                    if ($dkec->kec_id == $data->kec_id) {
                        $invalid = $invalid + intval($data->vote_tidaksah);
                        $vote_sah = json_decode($data->vote_sah);
                        foreach ($vote_sah as $vs) {
                            if ($vs->uuid == $value->uuid_paslon) {
                                $valid = $valid + intval($vs->point);
                            }
                        }
                    }
                }
                $sah[$dk] = $valid;
                $tidaksah[$dk] = $invalid;
            }
            $res[$key] += [
                'sah'       => $sah,
                'tidaksah'  => $tidaksah,
                'total'     => array_sum($sah) + array_sum($tidaksah),  
            ];
        }

        return $res;
    }
}
