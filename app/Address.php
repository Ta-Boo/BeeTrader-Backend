<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address';

    public $timestamps = false;
    protected $casts = [
        'longitude' => 'float',
        'latitude' => 'float',
    ];
    protected $fillable = [
        'longitude', 'latitude'
    ];
}
