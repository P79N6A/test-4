<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/9/21
 * Time: 11:30
 */

namespace app\Http\Models\Admin;
use Zizaco\Entrust\EntrustPermission;

class PermissionModel extends EntrustPermission
{

    protected $table = 'admin_permissions';

}