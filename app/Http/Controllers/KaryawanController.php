<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Karyawan;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Response;
use App\Helpers\GetKaryawanData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\Karyawan as KaryawanResource;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $karyawan = Karyawan::all()->map(fn($k)=>GetKaryawanData::getWithoutPassword($k,null));

            return ApiFormatter::createApi(200,ApiFormatter::$successMessages['get'],$karyawan);
        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'id'=> 'required',
            'nama'=> 'required',
            'email'=> 'required',
            'password'=> 'required',
            'alamat'=> 'required',
            'jabatan'=> 'required',
        ],[
            'required'=>'Wajib diisi'
        ]);
        try {

            Karyawan::create([
                'id'=> $request->id,
                'nama'=> $request->nama,
                'email'=> $request->email,
                'password'=> Hash::make($request->password),
                'alamat'=> $request->alamat,
                'jabatan'=> $request->jabatan,
                'role'=>'karyawan',
                'total_cuti_tahunan'=>12
            ]);

            return ApiFormatter::createApi(201,'Created Successfully');

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = Karyawan::find($id);
            $unread = request()->rle_id==0?Notification::karyawan($id)->unread()->get()->count():Notification::forAdmins()->unread()->get()->count();

            if ($data) return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], GetKaryawanData::getWithoutPassword($data,$unread));

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e); 
        }

        return ApiFormatter::createApi(404,'User Not Found');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit (Request $request)
    {
        $request->validate([
            'id'=> 'required',
            'nama'=> 'required',
            'email'=> 'required',
            'alamat'=> 'required',
            'jabatan'=> 'required',
        ],[
            'required'=>'Wajib diisi'
        ]);

        try {
            $karyawan = Karyawan::find($request->id);

            if ($karyawan)
            {
                $karyawan->id = $request->edit_id;
                $karyawan->nama = $request->nama;
                $karyawan->email = $request->email;
                $karyawan->alamat = $request->alamat;
                $karyawan->jabatan = $request->jabatan;

                $karyawan->save();
                return ApiFormatter::createApi(200, 'Success');
            }

            return ApiFormatter::createApi(404, 'Not Found');

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e); 
        }
    }

    
    public function edit_pass(Request $request)
    {
        try {
            $karyawan = Karyawan::find($request->id);

            if (!Hash::check($request->old_password,$karyawan->password)) {
                return ApiFormatter::createApi(400,'Password salah');
            }

            $request->validate([
                'old_password'=>'required',
                'new_password'=>'required|min:8',
                'new_password_confirmation'=>'required|same:new_password'
            ],
            [
                'required'=>'Wajib diisi',
                'min'=>'Password minimal 8 karakter',
                'same'=>'Konfirmasi password tidak cocok'
            ]);

            $karyawan->password = Hash::make($request->new_password);

            $karyawan->save();

            return ApiFormatter::createApi(200, 'Changed Successfully');

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e); 
        }
    }

    public function edit_profile(Request $request)
    {
        $request->validate([
            'image'=>'required'
        ]);

        $img_path = 'http://enkripa.test/storage/'.$request->file('image')->store('profile-images');

        try {
            $karyawan = Karyawan::find($request->id);
            
            if ($karyawan)
            {
                $karyawan->profile_image = $img_path;
                $karyawan->save();

                return ApiFormatter::createApi(200,'Changed Successfully');
            }

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'ids'=>'required'
        ]);

        try {
            Karyawan::destroy($request->ids);

            return ApiFormatter::createApi(200, 'Deleted Successfully');

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }
}
