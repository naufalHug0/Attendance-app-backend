<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Time;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Response;
use App\Models\Request_WFA;
use Illuminate\Http\RedirectResponse;
use App\Helpers\NotificationFormatter;
use App\Http\Requests\StoreRequest_WFARequest;
use App\Http\Requests\UpdateRequest_WFARequest;

class RequestWFAController extends Controller
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
        $today = Time::getToday();
        $request->validate([
            'id' => 'required',
            'date'=>"required|date|after_or_equal:$today",
            'location' => 'required'
        ],
        [
            'date.required'=>'Tanggal Wajib Diisi',
            'date.after_or_equal'=>'Masukkan tanggal yang valid',
            'location.required'=>'Lokasi Wajib Diisi',
        ]);

        if (isset($request->image)) $img_path = 'http://enkripa.test/storage/'.$request->file('image')->store('WFA-images');
        try 
        {
            $karyawan = Karyawan::find($request->id);

            if ($karyawan)
            {
                $format = NotificationFormatter::formatWFAInitial($karyawan->nama);

                if ($request->date!==Time::getToday())
                {
                    $notification = Notification::create([
                        'karyawan_id' => $karyawan->id,
                        'title'=>$format['title'],
                        'body'=>$format['body'],
                        'is_read'=>false,
                        'for'=>'admin'
                    ]);
                } else {
                    Absensi::create([
                        'karyawan_id'=>$karyawan->id,
                        'in'=>Time::getTimestamp(),
                        'out'=>null,
                        'status'=>'wfa',
                    ]);
                }

                Request_WFA::create([
                    'karyawan_id' => $karyawan->id,
                    'location' => $request->location,
                    'date' => $request->date,
                    'image' => isset($request->image) ? $img_path : null,
                    'is_approved' => $request->date == Time::getToday()?true:null,
                    'notification_id' => isset($notification)?$notification->id:null,
                ]);

                return ApiFormatter::createApi(201,'Inserted Successfully',$request->date);
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
            $wfa = Request_WFA::filterById($id)->today()->orRequestedToday()->get();

            if ($wfa) return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], $wfa);

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
        return ApiFormatter::createApi(404,'Data not found');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request_WFA $request_WFA)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try 
        {
            $wfa = Request_WFA::find($request->id);

            if ($wfa) 
            {
                $wfa->is_approved = $request->approved;
                $wfa->approved_by_id = $request->approved_by_id;

                $admin = Karyawan::find($request->approved_by_id);

                $format = $wfa->is_approved?NotificationFormatter::formatWFA_Approved($admin->nama):NotificationFormatter::formatWFARejected($admin->nama);

                Notification::create([
                    'karyawan_id' => $wfa->karyawan_id,
                    'for'=>'karyawan',
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                ]);

                if ($wfa->date===Time::getToday()&&$request->approved)
                {
                    Absensi::create([
                        'karyawan_id'=>$wfa->karyawan_id,
                        'in'=>Time::getTimestamp(),
                        'out'=>null,
                        'status'=>'wfa',
                    ]);
                }

                $wfa->save();
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
    public function destroy(Request_WFA $request_WFA)
    {
        //
    }
}
