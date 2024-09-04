<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = ['firstname', 'lastname', 'phone', 'email'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
