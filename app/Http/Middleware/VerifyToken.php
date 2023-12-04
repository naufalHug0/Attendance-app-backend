<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Symfony\Component\HttpFoundation\Response;

class VerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!$request->header('Authorization'))
            // return ApiFormatter::createApi(498, 'Invalid Token');
        try {
            $data = Karyawan::where('remember_token','=',$request->header('Authorization'))->first();
        } catch (Exception $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e); 
        }

        return $data?$next($request):ApiFormatter::createApi(498,'Invalid Token');
    }
}
