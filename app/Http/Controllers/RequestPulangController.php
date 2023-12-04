<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Helpers\NotificationFormatter;
use App\Helpers\Time;
use App\Http\Requests\StoreRequest_PulangRequest;
use App\Http\Requests\UpdateRequest_PulangRequest;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Notification;
use App\Models\Request_Pulang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestPulangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try
        {
            $karyawan = Karyawan::find($request->id);

            if ($karyawan)
            {
                $format = ($request->approve)?NotificationFormatter::pulangApproved():NotificationFormatter::pulangRejected();
                $request_pulang = Request_Pulang::find($request->req_pulang_id);

                if ($request_pulang)
                {
                    Notification::create([
                    'karyawan_id' => $karyawan->id,
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                    'for'=>'karyawan'
                    ]);

                    $request_pulang->is_approved = $request->approve;
                    $request_pulang->save();
    
                    if ($request->approve)
                    {
                        $absensi = Absensi::attendanceByKaryawanId($request->id)->filterByToday()->haventCheckout()->hadir()->notLembur()->get();
    
                        $absensi->map(function ($item) {
                            $Date = Time::getTimestamp();
                            $item->out = $Date;
                            $item->save();
                        });
                    }
                    
                    return ApiFormatter::createApi(200,ApiFormatter::$successMessages['get']);
                }
            }
            return ApiFormatter::createApi(404, 'User not found');
        }
        catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'alasan'=>'required'
        ],[
            'required'=>'Wajib diisi'
        ]);

        try {
            $karyawan = Karyawan::find($request->id);

            if ($karyawan)
            {
                $format = NotificationFormatter::confirmPulang($karyawan->nama,$request->alasan);

                $notification = Notification::create([
                    'karyawan_id' => $karyawan->id,
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                    'for'=>'admin'
                ]);

                Request_Pulang::create([
                    'karyawan_id'=>$karyawan->id,
                    'notification_id'=>$notification->id,
                    'is_approved'=>null
                ]);

                return ApiFormatter::createApi(201,'Created successfully');
            }

            return ApiFormatter::createApi(404, 'User not found');
        } 
        catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest_PulangRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request_Pulang $request_Pulang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request_Pulang $request_Pulang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest_PulangRequest $request, Request_Pulang $request_Pulang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request_Pulang $request_Pulang){
    }
}
