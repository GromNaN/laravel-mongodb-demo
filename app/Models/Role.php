<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Role as BaseRole;
use Jenssegers\Mongodb\Eloquent\DocumentModel;

class Role extends BaseRole
{
    use DocumentModel;

    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
