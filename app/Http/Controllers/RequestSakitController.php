<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Helpers\NotificationFormatter;
use App\Models\Request_Sakit;
use App\Http\Requests\StoreRequest_SakitRequest;
use App\Http\Requests\UpdateRequest_SakitRequest;

class RequestSakitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $img_path = null;
        if (isset($request->image)) $img_path = 'http://enkripa.test/storage/'.$request->file('image')->store('sakit-images');

        try {
            $karyawan = Karyawan::find($request->id);
            
            if ($karyawan)
            {
                $format = NotificationFormatter::formatSakit($karyawan->nama);

                $notification = Notification::create([
                    'karyawan_id' => $karyawan->id,
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                    'for'=>'admin'
                ]);

                Request_Sakit::create([
                    'karyawan_id'=>$karyawan->id,
                    'image'=>$img_path,
                    'notification_id'=>$notification->id
                ]);

                Absensi::create([
                    'karyawan_id'=>$karyawan->id,
                    'in'=>null,
                    'out'=>null,
                    'status'=>'sakit',
                ]);
                return ApiFormatter::createApi(201,'Inserted Successfully');
            }

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
        return ApiFormatter::createApi(404,'User not found');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $sakit = Request_Sakit::karyawan($id)->today()->get();

            if (count($sakit)>0) return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], $sakit[0]);

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
        return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request_Sakit $request_Sakit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $img_path = 'http://enkripa.test/storage/'.$request->file('image')->store('sakit-images');

        try {
            $karyawan = Karyawan::find($request->id);
            
            if ($karyawan)
            {
                $sakit = Request_Sakit::karyawan($karyawan->id)->today()->get();
                if (count($sakit)>0)
                {
                    $sakit = $sakit[0];
                    $sakit->image = $img_path;
                    $sakit->save();
                    return ApiFormatter::createApi(201,'Inserted Successfully');
                }
            }

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
        return ApiFormatter::createApi(404,'User not found');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request_Sakit $request_Sakit)
    {
        //
    }
}
