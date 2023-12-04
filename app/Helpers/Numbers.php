<?php

namespace App\Helpers;

class Numbers {
    public static function is_decimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}