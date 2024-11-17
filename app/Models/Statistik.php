<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Statistik extends Model
{
    use HasFactory;

    public function statistik_kecamatan()
    {
        $kec = DB::table('data_kecamatan')->orderBy('kec_id')->get();
        $paslons = DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
        $res = [];

        // per kecamatan
        foreach ($kec as $key => $value) {
            $votes = DB::table('data_voting')->where('kec_id', $value->kec_id)->where('tahun_vote', date('Y'))->get();
            $total = 0;
            $invalid = 0;
            $data_paslon = [];

            // paslon init
            foreach ($paslons as $ps) {
                $data_paslon[] = [
                    'uuid'  => $ps->uuid_paslon,
                    'nama'  => $ps->nama_paslon,
                    'urut'  => $ps->no_urut,
                    'foto'  => $ps->foto_paslon,
                    'voting'=> 0,
                ];
            }

            foreach ($votes as $vote) {
                $total = $total + intval($vote->total_vote);
                $invalid = $invalid + intval($vote->vote_tidaksah);
                $arr_paslon = json_decode($vote->vote_sah);
                foreach ($arr_paslon as $a => $arrp) {
                    foreach ($data_paslon as $d => $dp) {
                        if ($arrp->uuid == $dp['uuid']) {
                            $data_paslon[$d]['voting'] = intval($dp['voting']) + intval($arrp->point);
                        }
                    }
                }
            }

            $res[] = [
                'id'            => $value->id,
                'kec_name'      => $value->kec_name,
                'kec_id'        => $value->kec_id,
                'total'         => $total,
                'invalid'       => $invalid,
                'paslons'       => $data_paslon,
            ];
        }

        return $res;
    }

    public function statistik_desa($kec)
    {
        $desas   = DB::table('data_desa')->where('kec_id', $kec)->get();
        $paslons = DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
        $res     = [];
        foreach ($desas as $d => $desa) {
            $res[$d] = [
                'name'      => $desa->desakel_name,
                'full_id'   => $desa->full_id,
                'kec_id'    => $desa->kec_id,
            ];

            $tps = DB::table('data_voting')->where('tahun_vote', date('Y'))->where('desakel_id', $desa->full_id)->get();
            if (count($tps) > 0) {
                // $vote = DB::table('data_voting')->where('tahun_vote', date('Y'))->where('desakel_id', $desa->full_id)->first();
                // $res[$d] += [
                //     'total'     => $vote->total_vote,
                //     'valid'     => json_decode($vote->vote_sah),
                //     'invalid'   => $vote->vote_tidaksah,
                // ];
                $tps_total = 0;
                $tps_valid = [];
                $tps_invalid = 0;
                foreach ($paslons as $paslon) {
                    $tps_valid[] = [
                        'uuid'  => $paslon->uuid_paslon,
                        'name'  => $paslon->nama_paslon,
                        'point' => 0,

                    ];
                }

                foreach ($tps as $key => $value) {
                    // $vote = DB::table('data_voting')->where('tahun_vote', date('Y'))->where('desakel_id', $desa->full_id)->where('id', $value->id)->first();
                    $tps_total = $tps_total + intval($value->total_vote);
                    $tps_invalid = $tps_invalid + intval($value->vote_tidaksah);
                    $tps_paslon = json_decode($value->vote_sah);

                    foreach ($tps_paslon as $t => $tp) {
                        foreach ($tps_valid as $v => $tv) {
                            if ($tp->uuid == $tv['uuid']) {
                                $sum = intval($tv['point']) + intval($tp->point);
                                $tps_valid[$v]['point'] = $sum;
                            }
                        }
                    }
                }
                // akumulasi
                $res[$d] += [
                    'total'     => $tps_total,
                    'valid'     => $tps_valid,
                    'invalid'   => $tps_invalid,
                ];
            } else {
                $vote = [];
                foreach ($paslons as $paslon) {
                    $vote[] = [
                        'uuid'  => $paslon->uuid_paslon,
                        'name'  => $paslon->nama_paslon,
                        'point' => 0,

                    ];
                }
                
                $res[$d] += [
                    'total'     => 0,
                    'valid'     => $vote,
                    'invalid'   => 0,
                ];
            }
        }

        return $res;
    }

    public function statistik_tps($kec)
    {
        $data_tps = DB::table('data_dpt as dpt')
                    ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'dpt.full_id')
                    ->select('dpt.*', 'dd.desakel_name')
                    ->where('dpt.kec_id', $kec)->orderBy('dpt.full_id', 'asc')->orderBy('dpt.no_tps', 'asc')->get();
        $paslons  = DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
        $res      = [];
        foreach ($data_tps as $d => $tps) {
            $vtps = DB::table('data_voting')->where('tahun_vote', date('Y'))->where('desakel_id', $tps->full_id)->where('dpt_id', $tps->id)->first();
            $total = 0;
            $valid = 0;
            $invalid = 0;
            $data_paslon = [];

            // paslon init
            foreach ($paslons as $ps) {
                $data_paslon[] = [
                    'uuid'  => $ps->uuid_paslon,
                    'nama'  => $ps->nama_paslon,
                    'urut'  => $ps->no_urut,
                    'foto'  => $ps->foto_paslon,
                    'voting'=> 0,
                ];
            }

            if (!empty($vtps->vote_sah)) {
                $arr_paslon = json_decode($vtps->vote_sah);
                $total = intval($vtps->total_vote);
                $invalid = intval($vtps->vote_tidaksah);
                foreach ($arr_paslon as $a => $arrp) {
                    foreach ($data_paslon as $d => $dp) {
                        if ($arrp->uuid == $dp['uuid']) {
                            $data_paslon[$d]['voting'] = intval($dp['voting']) + intval($arrp->point);
                        }
                    }
                    $valid = $valid + intval($arrp->point);
                }
            }

            $res[] = [
                'name'      => ($tps->no_tps < 10 ? ('00'.$tps->no_tps) : (($tps->no_tps > 9 && $tps->no_tps < 100) ? ('0'.$tps->no_tps) : $tps->no_tps)),
                // 'name'      => $tps->no_tps,
                'desa'      => $tps->desakel_name,
                'full_id'   => $tps->full_id,
                'kec_id'    => $tps->kec_id,
                'total'     => $total,
                'valid'     => $valid,
                'invalid'   => $invalid,
                'paslon'    => $data_paslon,
            ];
        }

        return $res;
    }

    public function tabel_kecdesatps()
    {
        $data_kec = DB::table('data_kecamatan')->orderBy('id')->get();
        $data_paslon = DB::table('data_paslon')->where('tahun', date('Y'))->orderBy('no_urut')->get();
        $res = [];

        foreach ($data_kec as $k => $isKec) {
            $data_desa = DB::table('data_desa')->where('kec_id', $isKec->kec_id)->orderBy('full_id')->get();
            $tps_total = 0;
            $res_desa = [];
            $invalid = 0;
            $valid = 0;
            $total = 0;
            $dpt = 0;

            $paslon_kec = [];
            foreach ($data_paslon as $ps) {
                $paslon_kec[] = [
                    'uuid'  => $ps->uuid_paslon,
                    'nama'  => $ps->nama_paslon,
                    'urut'  => $ps->no_urut,
                    'foto'  => $ps->foto_paslon,
                    'voting'=> 0,
                ];
            }

            //data desa
            foreach ($data_desa as $d => $isDesa) {
                $data_tps = DB::table('data_dpt')->where('full_id', $isDesa->full_id)->get();
                $tps_total = $tps_total + count($data_tps);
                $res_tps = [];
                $_invalid = 0;
                $_valid = 0;
                $_total = 0;
                $_dpt = 0;

                $paslon_desa = [];
                foreach ($data_paslon as $ps) {
                    $paslon_desa[] = [
                        'uuid'  => $ps->uuid_paslon,
                        'nama'  => $ps->nama_paslon,
                        'urut'  => $ps->no_urut,
                        'foto'  => $ps->foto_paslon,
                        'voting'=> 0,
                    ];
                }

                //data TPS
                foreach ($data_tps as $t => $is_tps) {
                    $data_voting = DB::table('data_voting')->where('dpt_id', $is_tps->id)->where('tahun_vote', $is_tps->tahun_dpt)->first();
                    $__invalid = 0;
                    $__valid = 0;
                    $__total = 0;
                    $__dpt = intval($is_tps->total);

                    $paslon_tps = [];
                    foreach ($data_paslon as $ps) {
                        $paslon_tps[] = [
                            'uuid'  => $ps->uuid_paslon,
                            'nama'  => $ps->nama_paslon,
                            'urut'  => $ps->no_urut,
                            'foto'  => $ps->foto_paslon,
                            'voting'=> 0,
                        ];
                    }

                    if ($data_voting) {
                        $paslon_tps = json_decode($data_voting->vote_sah);
                        $__invalid = intval($data_voting->vote_tidaksah);
                        $__total = intval($data_voting->total_vote);

                        foreach ($paslon_tps as $a => $arrp) {
                            foreach ($paslon_desa as $d => $dp) {
                                if ($arrp->uuid == $dp['uuid']) {
                                    $paslon_desa[$d]['voting'] = intval($dp['voting']) + intval($arrp->point);
                                }
                            }
                            foreach ($paslon_kec as $c => $pk) {
                                if ($arrp->uuid == $pk['uuid']) {
                                    $paslon_kec[$c]['voting'] = intval($pk['voting']) + intval($arrp->point);
                                }
                            }
                            $__valid = $__valid + intval($arrp->point);
                        }

                        $_invalid = $_invalid + $__invalid;
                        $_valid = $_valid + $__valid;
                        $_total = $_total + $__total;
                    }
                    $_dpt = $_dpt + $__dpt;

                    $res_tps[] = [
                        'id'            => $is_tps->id,
                        'no_tps'        => $is_tps->no_tps,
                        'full_id'       => $is_tps->full_id,
                        'total'         => $__total,
                        'valid'         => $__valid,
                        'invalid'       => $__invalid,
                        'paslons'       => $paslon_tps,
                        'dpt'           => $__dpt,
                    ];
                }

                $invalid = $invalid + $_invalid;
                $valid = $valid + $_valid;
                $total = $total + $_total;
                $dpt = $dpt + $_dpt;

                $res_desa[] = [
                    'id'            => $isDesa->id,
                    'desakel_name'  => $isDesa->desakel_name,
                    'full_id'       => $isDesa->full_id,
                    'total'         => $_total,
                    'valid'         => $_valid,
                    'invalid'       => $_invalid,
                    'paslons'       => $paslon_desa,
                    'tps'           => $res_tps,
                    'tps_total'     => count($data_tps),
                    'dpt'           => $_dpt,
                ];
            }

            $res[] = [
                'id'            => $isKec->id,
                'kec_name'      => $isKec->kec_name,
                'kec_id'        => $isKec->kec_id,
                'total'         => $total,
                'valid'         => $valid,
                'invalid'       => $invalid,
                'paslons'       => $paslon_kec,
                'desas'         => $res_desa,
                'tps_total'     => $tps_total,
                'dpt'           => $dpt,
            ];
        }

        return $res;
    }
}
