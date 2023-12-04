<?php 

namespace App\Helpers;
class NotificationFormatter {
    public static function formatCutiInitial($nama)
    {
        return [
            'title'=>$nama.' telah mengajukan cuti',
            'body'=>$nama.' telah mengajukan cuti selama kurun waktu berikut : ',
        ];
    }
    
    public static function formatCutiApproved($nama)
    {
        return [
            'title'=>'Pengajuan cutimu telah disetujui',
            'body'=>'Pengajuan cutimu telah disetujui oleh '.$nama,
        ];

    } 
    public static function formatCutiRejected($nama)
    {
        return [
            'title'=>'Pengajuan cutimu telah ditolak',
            'body'=>'Pengajuan cutimu telah ditolak oleh '.$nama,
        ];

    } 

    public static function formatWFHInitial($nama)
    {
        return [
            'title'=>$nama.' telah mengajukan WFH',
            'body'=>$nama.' telah mengajukan WFH untuk tanggal : ',
        ];
    }

    public static function formatWFHApproved($nama)
    {
        return [
            'title'=>'Pengajuan WFHmu telah disetujui',
            'body'=>'Pengajuan WFHmu telah disetujui oleh '.$nama,
        ];

    }

    public static function formatWFHRejected($nama)
    {
        return [
            'title'=>'Pengajuan WFHmu telah ditolak',
            'body'=>'Pengajuan WFHmu telah ditolak oleh '.$nama,
        ];

    }
    
    public static function formatWFAInitial($nama)
    {
        return [
            'title'=>$nama.' telah mengajukan WFA',
            'body'=>$nama.' telah mengajukan WFA untuk tanggal : ',
        ];
    }

    public static function formatWFA_Approved($nama)
    {
        return [
            'title'=>'Pengajuan WFAmu telah disetujui',
            'body'=>'Pengajuan WFAmu telah disetujui oleh '.$nama,
        ];

    }
    
    public static function formatWFARejected($nama)
    {
        return [
            'title'=>'Pengajuan WFAmu telah ditolak',
            'body'=>'Pengajuan WFAmu telah ditolak oleh '.$nama,
        ];

    } 

    public static function formatSakit($nama)
    {
        return [
            'title'=>$nama.' sakit',
            'body'=>$nama.' hari ini tidak masuk karena sakit.',
        ];
    }

    public static function confirmPulang($nama,$alasan)
    {
        return [
            'title'=>$nama.' izin pulang',
            'body'=>$nama.' hari ini izin pulang lebih awal dengan alasan : "'.$alasan.'".',
        ];
    }

    public static function pulangApproved()
    {
        return [
            'title'=>'Perizinan pulang lebih awal disetujui',
            'body'=>'Perizinan anda untuk pulang lebih awal telah disetujui.',
        ];
    }

    public static function pulangRejected()
    {
        return [
            'title'=>'Perizinan pulang lebih awal ditolak',
            'body'=>'Perizinan anda untuk pulang lebih awal telah ditolak. Anda tidak dapat pulang lebih awal.',
        ];
    }

    public static function confirmCheckout($nama,$alasan)
    {
        return [
            'title'=>'Konfirmasi '.$nama,
            'body'=>$nama.' kemarin tidak mengakhiri absensinya dengan alasan : "'.$alasan.'".',
        ];
    }

    public static function forgotPassword($nama,$password)
    {
        return [
            'title'=>"$nama lupa password",
            'body'=>"Jangan beritahu ke siapapun kecuali $nama, Password baru: $password",
        ];
    }
}