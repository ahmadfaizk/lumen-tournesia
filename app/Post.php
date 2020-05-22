<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model 
{
    protected $fillable = [
        'name', 'description', 'address', 'province', 'city', 'id_category', 'id_user'
    ];
}