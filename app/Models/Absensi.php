<?php

namespace App\Models;

use App\Helpers\GetKaryawanData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function karyawans() {
        return $this->belongsTo(Karyawan::class,
    'karyawan_id');
    }

    public static function scopeKaryawansWithIds($query,array $ids) {
        return $query->whereIn('karyawan_id',$ids);
    }

    public static function scopeBetween($query,$start,$end)
    {
        return $query->whereDate('created_at','>=',$start)->whereDate('created_at','<=',$end);
    }

    public static function scopeFilterByToday($query) {
        return $query->whereDate('created_at', Carbon::today());
    }

    public static function scopeAttendanceByKaryawanId($query,$id) {
        return $query->where('karyawan_id', '=', $id);
    }

    public static function scopeFilterByMonth($query,$year,$month)
    {
        return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
    }

    public static function scopeHadir($query) 
    {
        return $query->where('status', '!=', 'sakit')->where('status', '!=', 'cuti')->where('status', '!=', '');
    }

    public static function scopeStatus($query,$status)
    {
        return $query->where('status',$status);
    }

    public static function scopeStatusAll($query,$status)
    {
        return $query->whereIn('status',$status);
    }

    public static function scopeWfo($query) 
    {
        return $query->where('status','wfo');
    }

    public static function scopeWfa($query) 
    {
        return $query->where('status','wfa');
    }

    public static function scopeWfh($query) 
    {
        return $query->where('status','wfh');
    }


    public static function scopeCuti($query) 
    {
        return $query->where('status','cuti');
    }

    public static function scopeSakit($query) {
        return $query->where('status', 'sakit');
    }

    public static function scopeTanpaKeterangan($query)
    {
        return $query->where('status', '');
    }

    public static function scopeNotLembur($query)
    {
        return $query->where('status', '!=', 'lembur');
    }

    public static function scopeOrUncheckedOutLembur($query)
    {
        return $query->orWhere('status', 'lembur')->where('out', null);
    }

    public static function scopeHaventCheckout($query)
    {
        return $query->where('out', null);
    }

    public static function scopeYesterday($query)
    {
        return $query->whereDate('created_at', Carbon::yesterday());
    }

}
