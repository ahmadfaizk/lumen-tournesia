<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model 
{
    //protected $table = 'categories';

    protected $fillable = [
        'id_province', 'name'
    ];

    public $timestamps = false;
}