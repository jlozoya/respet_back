<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Traits\ResetsPasswords;

class PasswordController extends BaseController
{
    use ResetsPasswords;

    public function __construct()
    {
        $this->broker = 'users';
    }
}