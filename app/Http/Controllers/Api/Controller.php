<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Responses\ApiResponse;

abstract class Controller extends BaseController
{
    use ApiResponse;
}
