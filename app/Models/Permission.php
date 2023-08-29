<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Permission as BasePermission;
use Jenssegers\Mongodb\Eloquent\DocumentModel;

class Permission extends BasePermission
{
    use DocumentModel;

    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
