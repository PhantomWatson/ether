<?php

use Phinx\Migration\AbstractMigration;

class RemoveTimestampFromUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('timestamp');
    }
}
