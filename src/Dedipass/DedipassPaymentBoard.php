<?php

namespace App\Dedipass;

use App\Dedipass\Database\DedipassTable;
use App\Shop\Payment\AbstractPaymentBoard;

class DedipassPaymentBoard extends AbstractPaymentBoard
{
    protected string $entity = DedipassPaymentType::class;
    protected string $type = 'dedipass';
    private bool $remove = true;
    protected $table;

    public function __construct(DedipassTable $table)
    {
        $this->table = $table;
    }

    protected function makeAbstractQuery(): \ClientX\Database\Query
    {
        return $this->table->makeQuery()
            ->select('COALESCE(SUM(amount), 0) as total')
            ->where('status = :state')
            ->params(['state' => 'success']);
    }

    public function clearBoard(): bool
    {
        return $this->table->makeQuery()
            ->setCommand("DELETE")
            ->execute() != false;
    }


}