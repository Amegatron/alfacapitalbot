<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpifCourse extends Model
{
    protected $table = 'opif_courses';
    public $timestamps = true;

    protected $fillable = [
        'date',
        'course',
        'opif_id',
        'publishedAt',
    ];
}
