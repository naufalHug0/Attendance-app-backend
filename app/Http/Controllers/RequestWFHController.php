<?php

namespace App\Http\Controllers;

use App\Helpers\Time;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Request_WFH;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Helpers\NotificationFormatter;
use App\Http\Requests\StoreRequest_WFHRequest;
use App\Http\Requests\UpdateRequest_WFHRequest;

class RequestWFHController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $karyawan = Karyawan::find($request->id);
            
            if ($karyawan) {
                $format = NotificationFormatter::formatWFHInitial($karyawan->nama);

                $notification = Notification::create([
                    'karyawan_id' => $request->id,
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                    'for'=>'admin'
                ]);

                Request_WFH::create([
                    'karyawan_id' => $request->id,
                    'is_approved'=>null,
                    'notification_id' => $notification->id
                ]);

                return ApiFormatter::createApi(201,'Inserted Successfully');
            }
        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
        return ApiFormatter::createApi(404,'User not found');
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
    public function store(StoreRequest_WFHRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $wfh = Request_WFH::filterById($id)->today()->get();

            if ($wfh) return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], $wfh);

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request_WFH $request_WFH)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $wfh = Request_WFH::find($request->id);
            $admin = Karyawan::find($request->approved_by_id);

            if ($wfh) {
                $wfh->is_approved = $request->approved;
                $wfh->approved_by_id = $request->approved_by_id;

                $format = $wfh->is_approved?NotificationFormatter::formatWFHApproved($admin->nama):NotificationFormatter::formatWFHRejected($admin->nama);

                Notification::create([
                    'karyawan_id' => $wfh->karyawan_id,
                    'for'=>'karyawan',
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                    'request_id'=>$wfh->id,
                ]);

                if ($request->approved)
                {
                    Absensi::create([
                        'karyawan_id'=>$wfh->karyawan_id,
                        'in'=>Time::getTimestamp(),
                        'out'=>null,
                        'status'=>'wfh',
                    ]);
                }
                
                $wfh->save();
                return ApiFormatter::createApi(200, 'Success');
            }

            return ApiFormatter::createApi(404, 'Not Found');

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request_WFH $request_WFH)
    {
        //
    }
}
