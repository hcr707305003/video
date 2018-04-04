<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SonStand extends Model
{
    protected $table = 'son_stand';

    protected $guarded = ['token']; //黑名单在里面的都不能赋值
}
