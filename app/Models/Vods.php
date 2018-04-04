<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vods extends Model
{
    use SoftDeletes;
    protected $table = 'vods';

    protected $guarded = ['token']; //黑名单在里面的都不能赋值


}
