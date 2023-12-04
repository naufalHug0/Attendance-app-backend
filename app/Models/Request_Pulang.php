<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request_Pulang extends Model
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
}
