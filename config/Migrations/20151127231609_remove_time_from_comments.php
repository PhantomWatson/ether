<?php

use Phinx\Migration\AbstractMigration;

class RemoveTimeFromComments extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('time');
    }
}
