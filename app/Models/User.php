<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

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

    public function list_user()
    {
        return DB::table('users as u')
                // ->leftJoin('sessions as ss', 'ss.user_id', '=', 'u.id')
                ->leftJoin('data_kecamatan as dk', 'dk.kec_id', '=', 'u.kode')
                ->leftJoin('data_desa as dd', 'dd.full_id', '=', 'u.kode')
                ->select('u.uuid', 'u.name', 'u.email', 'u.level', 'u.kode', 'u.created_at', 'dk.kec_name', 'dd.desakel_name')
                ->orderBy('u.name')->get();
    }
}
