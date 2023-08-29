<?php

namespace App\Models;

use Encore\Admin\Auth\Database\OperationLog as BaseOperationLog;
use Jenssegers\Mongodb\Eloquent\DocumentModel;

class OperationLog extends BaseOperationLog
{
    use DocumentModel;

    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
