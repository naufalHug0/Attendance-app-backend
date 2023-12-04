<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Admin;
use App\Models\Karyawan;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\NotificationFormatter;
use App\Models\Request_Forgot_Pass;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function login(Request $request) {
        try {
            $user = Karyawan::where('email',$request->email)->first();

            if (!$user || !Hash::check($request->password,$user->password)) {
                return ApiFormatter::createApi(401,'Invalid email or password');
            }

            if ($request->role ==='admin') 
            {
                $is_admin = Admin::where('karyawan_id', $user->id)->first();
                if (!$is_admin) return ApiFormatter::createApi(401,'Invalid email or password');
            }
            
            $user->update(['remember_token'=>Hash::make($user->id)]);
            $user->save();

            return ApiFormatter::createApi(200,'Login success',[
                'id'=>$user->id,
                'token'=>$user->remember_token
            ]);

        } catch (Exception $e) {
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            $user = Karyawan::where('email',$request->email)->first();

            if ($user)
            {
                $new_password = Hash::make($user->nama);

                $format = NotificationFormatter::forgotPassword($user->nama,$new_password);
                
                Notification::create([
                    'karyawan_id' => $user->id,
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                    'for'=>'admin'
                ]);

                $user->password = $new_password;
                $user->save();

                return ApiFormatter::createApi(200,'Success'); 
            }

            return ApiFormatter::createApi(404,'User not found');

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
