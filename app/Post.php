<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model 
{
    protected $fillable = [
        'name', 'description', 'province', 'city', 'id_category', 'id_user'
    ];
}