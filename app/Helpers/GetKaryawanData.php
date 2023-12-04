<?php 
namespace App\Helpers;
class GetKaryawanData {
    public static function getWithoutPassword($data,$unread) {
        return [
            "id" => $data->id,
            "nama"=>$data->nama,
            "email"=>$data->email,
            "alamat"=>$data->alamat,
            "jabatan"=>$data->jabatan,
            "profile_image"=>$data->profile_image,
            "role"=>$data->role,
            "total_cuti"=>intval($data->total_cuti_tahunan),
            "unread_notifications"=>$unread
        ];
    }

    public static function forReport($data)
    {
        return [
            "id" => $data->id,
            "nama"=>$data->nama,
            "email"=>$data->email,
            "alamat"=>$data->alamat,
            "jabatan"=>$data->jabatan,
        ];
    }
}