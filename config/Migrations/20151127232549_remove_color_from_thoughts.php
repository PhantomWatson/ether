<?php

use Phinx\Migration\AbstractMigration;

class RemoveColorFromThoughts extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->removeColumn('color');
    }
}
