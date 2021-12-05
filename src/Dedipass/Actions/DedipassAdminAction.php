<?php

namespace App\Dedipass\Actions;


use App\Dedipass\Database\DedipassTable;
use App\Shop\Database\TransactionTable;
use ClientX\Actions\Payment\PaymentAdminAction;
use ClientX\Database\Query;
use ClientX\Renderer\RendererInterface;
use ClientX\Router;
use ClientX\Session\FlashService as Flash;
use Psr\Http\Message\ServerRequestInterface as Request;

class DedipassAdminAction extends PaymentAdminAction
{

    protected $routePrefix = "dedipass.admin";
    protected $moduleName = "Dedipass";
    protected $paymenttype = "Dedipass";
    protected $viewPath = "@Dedipass_admin";
    /**
     * @var \App\Dedipass\Database\DedipassTable
     */
    private DedipassTable $dedipassTable;

    public function __construct(RendererInterface $renderer, TransactionTable $table, Flash $flash, Router $router, DedipassTable $dedipassTable)
    {
        parent::__construct($renderer, $table, $flash, $router);
        $this->dedipassTable = $dedipassTable;
    }

    public function findItems(array $params): Query
    {
        return $this->dedipassTable->makeQueryForAdmin($params);
    }

    public function findItem(int $id)
    {
        return $this->dedipassTable->find($id);
    }

}