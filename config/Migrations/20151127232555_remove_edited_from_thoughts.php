<?php

use Phinx\Migration\AbstractMigration;

class RemoveEditedFromThoughts extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->removeColumn('edited');
    }
}
