<?php

namespace App\Http\Controllers;

use App\Helpers\Time;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use App\Helpers\ArrayUtilities;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\requests\Request_Sakit;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Notification::with('request_sakits', 'request_cutis', 'request_w_f_a_s', 'request_w_f_h_s','request__pulangs')->sortByDate()->forAdmins()->get();

            return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'],$data);

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    public function getKaryawan(Request $request)
    {
        try {
            $data = Notification::karyawan($request->id)->sortByDate()->get();

            return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'],$data);

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
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
    public function store(StoreNotificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = Notification::with('request_sakits','request_cutis','request_w_f_a_s','request_w_f_h_s','request__pulangs')->find($id);

            return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'],$data);

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        try {
            $data = Notification::find($id);

            $data->is_read = true;

            $data->save();

            return ApiFormatter::createApi(201, 'Updated succesfully');

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
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
            Notification::destroy($request->ids);

            return ApiFormatter::createApi(200, 'Deleted Successfully');

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }
}
