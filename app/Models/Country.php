<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Country extends Model
{
    use HasFactory;

    /**
     * @param string $dialCode
     * @return mixed
     */
    public static function getCountryByDialCode($dialCode) {
        return DB::table('countries')
            ->select('id')
            ->from('countries')
            ->where('dial_code', '=', $dialCode)
            ->first()->id;

    }
}
