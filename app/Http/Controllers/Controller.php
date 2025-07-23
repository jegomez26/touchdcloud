<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // Important part

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}