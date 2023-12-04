<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request_WFA extends Model
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

    public static function scopeToday($query)
    {
        return $query->whereDate('date',Carbon::today());
    }

    public static function scopeApproveIsNull($query) 
    {
        return $query->where('is_approved', null);
    }

    public static function scopeOrRequestedToday($query)
    {
        return $query->orWhereDate('created_at',Carbon::today());
    }
}
