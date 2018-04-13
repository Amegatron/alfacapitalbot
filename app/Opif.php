<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Opif
 * @package App
 *
 * @property string $name
 * @property string $fullName
 * @property string $publicDataUrl
 */
class Opif extends Model
{
    protected $table = 'opifs';
    public $timestamps = false;
    public $fillable = [
        'name',
        'fullName',
        'publicDataUrl',
    ];

    public function courses()
    {
        return $this->hasMany('App\OpifCourse', 'opif_id', 'id');
    }

    public function latestCourse()
    {
        return $this->courses()->orderBy('date', 'desc')->first();
    }
}
