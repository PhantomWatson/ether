<?php

use Phinx\Migration\AbstractMigration;

class RemoveOldFields extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('recipient');
        $table->removeColumn('sender');
    }
}
