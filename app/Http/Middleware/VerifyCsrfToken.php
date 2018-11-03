<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'user/regist',
        'user/login',
        'user/sms',
        'addresse/addAddress',
        'addresse/editAddress',
        'cart/addCart',
        'order/addorder',
        'user/changePassword',
        'user/forgetPassword',
    ];
}
