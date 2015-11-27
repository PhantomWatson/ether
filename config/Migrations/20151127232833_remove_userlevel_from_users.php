<?php

use Phinx\Migration\AbstractMigration;

class RemoveUserlevelFromUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('userlevel');
    }
}
