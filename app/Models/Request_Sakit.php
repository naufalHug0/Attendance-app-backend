<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request_Sakit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function notifications() {
        return $this->hasOne(\App\Models\Notification::class);
    }

    public function karyawans()
    {
        return $this->belongsTo(\App\Models\Karyawan::class);
    }

    public static function scopeToday($query) {
        return $query->whereDate('created_at', Carbon::today());
    }

    public static function scopeKaryawan($query,$id)
    {
        return $query->where('karyawan_id', '=', $id);
    }

    public function scopeSortByDate($query)
    {
            return $query->orderBy('created_at','DESC');
    } 

    public function scopeMonth($query,$month,$year)
    {
        return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
    }
}
