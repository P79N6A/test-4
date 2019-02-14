<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OperationFacade extends Facade {

    protected static function getFacadeAccessor() { return 'operation'; }

}