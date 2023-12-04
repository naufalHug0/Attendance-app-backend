<?php

namespace App\Helpers;

class ApiFormatter {
    protected static $response = [
        'status'=>null,
        'message'=>null,
        'data'=>null
    ];

    static $successMessages = [
        'get'=>'Data fetched successfully'
    ];

    static $errorMessages = [
        'get'=>'Error fetching data'
    ];

    public static function createApi($status=null,$message=null,$data=null) {
        self::$response = [
            'status'=>$status,
            'message'=>$message,
            'data'=>$data
        ];

        return response()->json(self::$response, self::$response['status']);
    }
}