<?php

namespace App\Dedipass\Actions;


use ClientX\Actions\Payment\PaymentAdminAction;

class DedipassAdminAction extends PaymentAdminAction
{

    protected $routePrefix = "dedipass.admin";
    protected $moduleName = "Dedipass";
    protected $paymenttype = "dedipass";
}