<?php

namespace App\Dedipass\Database;

use App\Dedipass\Dedipass;
use ClientX\Database\Query;

class DedipassTable extends \ClientX\Database\Table
{
    protected $table = "dedipass_log";
    protected $entity = Dedipass::class;
    protected $element = "code";


    public function makeQueryForAdmin(?array $search = null, $order = 'desc'): Query
    {
        $query =  parent::makeQueryForAdmin($search)
            ->select('d.*', 'CONCAT(u.firstname," ",u.lastname) as username', 'u.id as userId')
            ->join("users u", "u.id = d.user_id");
        $query->order = [];
        $query->order("d.id DESC");
        return $query;
    }
}
