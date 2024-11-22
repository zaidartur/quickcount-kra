<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Vote extends Model
{
    use HasFactory;

    public function get_tps($desa)
    {
        return DB::table('data_voting')->where('desakel_id', $desa)->where('tahun_vote', date('Y'))->select('desakel_name', 'no_tps', 'dpt_id', 'kec_id')->get();
    }

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
        $desas = DB::table('data_voting')->select('desakel_id')->where('tahun_vote', date('Y'))->where('kec_id', Auth::user()->kode)->groupBy('desakel_id')->get();
        $paslons = DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
        if (count($desas) > 0) {
            //data per desa
            foreach ($desas as $d => $desa) {
                $data_tps  = DB::table('data_voting')->where('desakel_id', $desa->desakel_id)->orderBy('no_tps')->get();
                $data_desa = DB::table('data_desa as dd')->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'dd.kec_id')
                            ->select('dk.kec_name', 'dd.desakel_name', 'dd.full_id', 'dd.kec_id', 'dd.desakel_id')
                            ->where('dd.full_id', $desa->desakel_id)->first();
                $total_dpt = DB::table('data_dpt')->where('full_id', $desa->desakel_id)->sum('total');
                $total_tps = DB::table('data_dpt')->where('full_id', $desa->desakel_id)->count();
                $tps = [];
                $data_paslon = [];
                $total = 0;
                $valid = 0;
                $invalid = 0;
                foreach ($paslons as $paslon) {
                    $data_paslon[] = [
                        'uuid'  => $paslon->uuid_paslon,
                        'name'  => $paslon->nama_paslon,
                        'point' => 0,
                    ];
                }

                //data per TPS
                if (count($data_tps) > 0) {
                    foreach ($data_tps as $t => $dt) {
                        $fetch_tps = DB::table('data_voting as dv')
                                    ->leftJoin('users as u', 'u.uuid', '=', 'dv.user')
                                    ->leftJoin('data_dpt as dpt', 'dpt.id', '=', 'dv.dpt_id')
                                    ->select('dv.*', 'u.name', 'u.level', 'u.kode', 'dpt.total')
                                    ->where('dv.id', $dt->id)->first();
                        $paslon_tps = json_decode($fetch_tps->vote_sah);
                        $_valid = 0;
                        foreach ($data_paslon as $p => $dp) {
                            foreach ($paslon_tps as $a => $pt) {
                                if ($pt->uuid == $dp['uuid']) {
                                    $add = intval($pt->point) + intval($dp['point']);
                                    $data_paslon[$p]['point'] = $add;
                                    $_valid = $_valid + intval($pt->point);
                                }
                            }
                        }
                        $invalid = $invalid + intval($fetch_tps->vote_tidaksah);
                        $valid = $valid + $_valid;
                        $total = $total + intval($fetch_tps->total_vote);
                        $tps[$t] = [
                            'id'        => $fetch_tps->id,
                            'uuid_vote' => $fetch_tps->uuid_vote,
                            'dpt_id'    => $fetch_tps->dpt_id,
                            'kec_id'    => $fetch_tps->kec_id,
                            'full_id'   => $fetch_tps->desakel_id,
                            'no_tps'    => $fetch_tps->no_tps,
                            'valid'     => $paslon_tps,
                            'invalid'   => intval($fetch_tps->vote_tidaksah),
                            'num_valid' => $_valid,
                            'total'     => $fetch_tps->total_vote,
                            'dpt'       => $fetch_tps->total,
                            'user'      => $fetch_tps->name,
                            'user_lvl'  => $fetch_tps->level,
                            'user_kode' => $fetch_tps->kode,
                        ];
                    }
                }

                $res[] = [
                    'kec_id'        => $data_desa->kec_id,
                    'kec_name'      => $data_desa->kec_name,
                    'desakel_id'    => $data_desa->desakel_id,
                    'desakel_name'  => $data_desa->desakel_name,
                    'full_id'       => $data_desa->full_id,
                    'valid'         => $data_paslon,
                    'invalid'       => $invalid,
                    'num_valid'     => $valid,
                    'total'         => $total,
                    'dpt'           => $total_dpt,
                    'tps'           => $tps,
                    'tps_total'     => $total_tps,
                ];
            }
        }

        return $res;
    }

    public function data_voting_kecamatan_old()
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
