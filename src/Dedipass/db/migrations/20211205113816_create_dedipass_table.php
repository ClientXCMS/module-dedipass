<?php

use Phinx\Migration\AbstractMigration;

class CreateDedipassTable extends AbstractMigration
{
    public function change()
    {
        $this->table("dedipass_log")
            ->addColumn("user_id", "integer")
            ->addColumn("code", "string")
            ->addColumn("payout", "float")
            ->addColumn("amount", "float")
            ->addColumn("status", "string")
            ->addColumn("identifier", "string")
            ->addTimestamps()
            ->create();
    }
}
