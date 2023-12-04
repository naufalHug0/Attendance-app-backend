<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Karyawan extends Model
{
    use HasApiTokens,HasFactory,Notifiable;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class,'karyawan_id');
    }

    public function request_cutis() 
    {
        return $this->hasMany(\App\Models\requests\Request_Cuti::class);
    }

    public function request_w_f_h_s() {
        return $this->hasMany(Request_WFH::class,'request_id');
    }

    public function request_w_f_a_s() {
        return $this->hasMany(Request_WFA::class,'request_id');
    }

    public function request_sakits() {
        return $this->hasMany(Request_Sakit::class,'request_id');
    }
}
