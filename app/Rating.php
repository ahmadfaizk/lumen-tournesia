<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model 
{
    protected $fillable = [
        'votes', 'comment', 'id_post', 'id_user',
    ];
}