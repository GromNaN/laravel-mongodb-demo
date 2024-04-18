<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Podcast extends Model
{
    protected $connection = 'mongodb';
}
