<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Menu as BaseMenu;
use Jenssegers\Mongodb\Eloquent\DocumentModel;

class Menu extends BaseMenu
{
    use DocumentModel;

    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
