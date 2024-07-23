<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\DocumentModel;

class PersonalAccessToken extends \Laravel\Sanctum\PersonalAccessToken
{
    use DocumentModel;

    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens';
    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
