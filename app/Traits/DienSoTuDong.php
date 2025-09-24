<?php
namespace App\Traits;
trait DienSoTuDong
{
    public static function laySttTiepTheo($column = 'stt')
    {
        $max = static::max($column);
        return $max ? $max + 1 : 1;
    }
}