<?php

use Phinx\Migration\AbstractMigration;

class RemoveStatusFromMessages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('status');
    }
}
