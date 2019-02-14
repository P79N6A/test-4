<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/9/18
 * Time: 17:18
 */

namespace app\Models;
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    protected $name;
    protected $display_name;
    protected $description;
}