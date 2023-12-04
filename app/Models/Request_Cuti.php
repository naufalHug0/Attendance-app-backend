<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request_Cuti extends Model
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

    public static function scopeFilterById($query,$id)
    {
        return $query->where('karyawan_id', $id);
    }

    public static function scopeNotOver($query)
    {
        return $query->whereDate('end','>=',Carbon::today());
    }

    public static function scopeStartsIn($query,$year,$month)
    {
        return $query->whereMonth('start', $month)->whereYear('start', $year);
    }

    public static function scopeEndsIn($query,$year,$month)
    {
        return $query->whereMonth('end', $month)->whereYear('end', $year);
    }

    public static function scopeOrEndsIn($query,$year,$month)
    {
        return $query->orWhereMonth('end', $month)->whereYear('end', $year);
    }

    public static function scopeOnCuti($query)
    {
        return $query->whereDate('start', '<=', Carbon::today())->whereDate('end', '>=', Carbon::today());
    }

    public static function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
