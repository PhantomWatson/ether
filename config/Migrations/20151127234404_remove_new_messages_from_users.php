<?php

use Phinx\Migration\AbstractMigration;

class RemoveNewMessagesFromUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('newMessages');
    }
}
