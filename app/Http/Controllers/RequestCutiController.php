<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\Notification;
use App\Models\Request_Cuti;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Response;
use \App\Models\Request_Cuti as Cuti;
use Illuminate\Http\RedirectResponse;
use \App\Helpers\NotificationFormatter;
use App\Http\Requests\StoreRequest_CutiRequest;
use App\Http\Requests\UpdateRequest_CutiRequest;
use DateInterval;

class RequestCutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $max_days_of_submission = new DateTime();
            $max_days_of_submission = $max_days_of_submission->add(new DateInterval("P3D"))->format('m/d/Y');
            $karyawan = Karyawan::find($request->id);
            
            if ($karyawan) {
                $total_cuti = $karyawan->total_cuti_tahunan;
                $max_date_cuti_tahunan = new Carbon($request->start);
                $max_date_cuti_tahunan = $max_date_cuti_tahunan->addDays($total_cuti);

                $request->validate([
                    'type' => 'required',
                    'start'=>"required|date|after_or_equal:$max_days_of_submission",
                    'end' => 'required|date|after:start'.$request->type==='tahunan'?"|before_or_equal:$max_date_cuti_tahunan":'',
                ],
                [
                    'start.required'=>'Tanggal Mulai Wajib Diisi',
                    'start.after_or_equal'=>'Pengajuan maksimal 3 hari sebelum tanggal cuti',
                    'end.before_or_equal'=>'Jumlah hari melebihi total cuti tahunan',
                    'end.required'=>'Tanggal Akhir Wajib Diisi',
                    'type.required'=>'Tipe Cuti Wajib Diisi',
                    'end.after'=>'Tanggal harus setelah tanggal mulai cuti',
                ]);

                $format = NotificationFormatter::formatCutiInitial($karyawan->nama);
                $notification = Notification::create([
                    'karyawan_id' => $request->id,
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                    'for'=>'admin'
                ]);

                Cuti::create([
                    'karyawan_id' => $request->id,
                    'start'=>$request->start,
                    'end' => $request->end,
                    'is_approved'=>null,
                    'type'=>$request->type,
                    'desc'=>$request->desc,
                    'notification_id'=>$notification->id
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
    public function store(StoreRequest_CutiRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $cuti = Cuti::filterById($id)->notOver()->get();

            if ($cuti) return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], $cuti);

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $cuti = Cuti::find($request->id);
            $admin = Karyawan::find($request->approved_by_id);
            
            if ($cuti) {
                $cuti->is_approved = $request->approved;
                $cuti->approved_by_id = $request->approved_by_id;

                $format = $cuti->is_approved?NotificationFormatter::formatCutiApproved($admin->nama):NotificationFormatter::formatCutiRejected($admin->nama);

                    Notification::create([
                        'karyawan_id' => $cuti->karyawan_id,
                        'for'=>'karyawan',
                        'title'=>$format['title'],
                        'body'=>$format['body'],
                        'is_read'=>false
                    ]);
                
                $cuti->save();
                return ApiFormatter::createApi(200, 'Success');
            }

            return ApiFormatter::createApi(404, 'Not Found',[$cuti,$admin]);

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
