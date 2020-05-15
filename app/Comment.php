<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model 
{
    protected $fillable = [
        'votes', 'comment', 'id_post', 'id_user',
    ];
}