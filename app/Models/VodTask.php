<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VodTask extends Model
{
    protected $table = 'vods_task';

    protected $guarded = ['token']; //黑名单在里面的都不能赋值

}
