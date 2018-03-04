<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opif extends Model
{
    protected $table = 'opifs';
    public $timestamps = false;

    // public $name;
    // public $fullName;
    // public $publicDataUrl;

    public function courses() {
        return $this->hasMany('App\OpifCourse', 'opif_id', 'id');
    }

    public function latestCourse() {
        return $this->courses()->orderBy('date', 'desc')->first();
    }
}
