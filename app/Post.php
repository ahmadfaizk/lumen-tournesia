<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model 
{
    protected $fillable = [
        'name', 'description', 'address', 'id_province', 'id_regency', 'id_category', 'id_user'
    ];
}