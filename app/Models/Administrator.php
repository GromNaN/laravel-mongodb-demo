<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator as BaseAdministrator;
use Jenssegers\Mongodb\Eloquent\DocumentModel;

class Administrator extends BaseAdministrator
{
    use DocumentModel;

    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
