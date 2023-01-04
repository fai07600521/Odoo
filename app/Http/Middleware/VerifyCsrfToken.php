<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/admin/pos/makeorder','/admin/pos/searchorder','/admin/pos/getbarcode','/admin/stock/store','/admin/promotions/discountprice/apigetproduct','/admin/promonotification/addproduct','/admin/promonotification/checkpromotion','/admin/promonotification/discountprice/apigetproduct','/admin/stock/poscheck','/admin/stock/productcheck','/stock/store','/admin/promotions/checkpromotion'
    ];
}
