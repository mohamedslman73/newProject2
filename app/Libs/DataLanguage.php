<?php


namespace App\Libs;

class DataLanguage{
    private static $Language;

    public static function set($Language){
        self::$Language = $Language;
    }

    public static function get(){
        return self::$Language;
    }
}


?>