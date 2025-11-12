<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesResources;

abstract class Controller
{
    use AuthorizesResources;
}
