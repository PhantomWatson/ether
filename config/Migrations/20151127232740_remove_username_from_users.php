<?php

use Phinx\Migration\AbstractMigration;

class RemoveUsernameFromUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('username');
    }
}
