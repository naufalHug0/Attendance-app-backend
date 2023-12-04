<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Response;
use App\Helpers\GetKaryawanData;
use Illuminate\Support\Facades\Hash;

class TokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            $data = Karyawan::where('id','=',$id)->first();
        } catch (Exception $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e); 
        }

        if ($data) {
            if (Hash::check($data->id, $data->remember_token)) {
                return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], GetKaryawanData::getWithoutPassword($data));
            } else {
                return ApiFormatter::createApi(498,'Invalid Token');
            }
        }

        return ApiFormatter::createApi(401,'User Not Found');
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
    public function destroy(Karyawan $karyawan)
    {
        //
    }
}
