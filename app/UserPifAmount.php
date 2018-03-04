<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPifAmount extends Model
{
    protected $table = 'user_pif_amounts';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'opif_id',
        'amount'
    ];
}
