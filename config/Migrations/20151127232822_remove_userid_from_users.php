<?php

use Phinx\Migration\AbstractMigration;

class RemoveUseridFromUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('userid');
    }
}
