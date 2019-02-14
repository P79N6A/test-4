<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/9/18
 * Time: 17:16
 */

namespace app\Models;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $name;
    protected $display_name;
    protected $description;

}