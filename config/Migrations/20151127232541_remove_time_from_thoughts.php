<?php

use Phinx\Migration\AbstractMigration;

class RemoveTimeFromThoughts extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->removeColumn('time');
    }
}
