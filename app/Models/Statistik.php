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

            if (DB::table('data_voting')->where('tahun_vote', date('Y'))->where('desakel_id', $desa->full_id)->exists()) {
                $vote = DB::table('data_voting')->where('tahun_vote', date('Y'))->where('desakel_id', $desa->full_id)->first();
                $res[$d] += [
                    'total'     => $vote->total_vote,
                    'valid'     => json_decode($vote->vote_sah),
                    'invalid'   => $vote->vote_tidaksah,
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
}
