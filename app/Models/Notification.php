<?php

namespace App\Models;

use App\Models\Request_Cuti;
use App\Models\Request_Sakit;
use App\Models\Request_WFH;
use App\Models\Request_WFA;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function karyawans() {
        return $this->belongsTo(Karyawan::class);
    }

    public function request_cutis() {
        return $this->hasMany(Request_Cuti::class, 'notification_id');
    }

    public function request_w_f_h_s() {
        return $this->hasMany(Request_WFH::class,'notification_id');
    }

    public function request_w_f_a_s() {
        return $this->hasMany(Request_WFA::class,'notification_id');
    }

    public function request_sakits() {
        return $this->hasMany(Request_Sakit::class,'notification_id');
    }

    public function request__pulangs() {
        return $this->hasMany(Request_Pulang::class,'notification_id');
    }

    public static function scopeFilter($query, $request_id, array $filters) {
        return $query->where('request_id', $request_id)->where('request_type',$filters['type']);
    }

    public function scopeSortByDate($query)
    {
        return $query->orderBy('created_at','DESC');
    } 

    public function scopeKaryawan($query,$id)
    {
        return $query->where('karyawan_id', $id)->where('for','karyawan');
    }

    public function scopeForAdmins($query)
    {
        return $query->where('for','admin');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read',false);
    }

    public function scopeForId($query,$id)
    {
        return $query->where('karyawan_id', $id);
    }
}
