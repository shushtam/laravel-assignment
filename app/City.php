<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'name2', 'name3', 'latitude', 'longitude', 'country', 'num1', 'num2', 'num3', 'zone', 'date'];
    public $timestamps = ['date'];
}
