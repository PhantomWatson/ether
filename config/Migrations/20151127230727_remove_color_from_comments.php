<?php

use Phinx\Migration\AbstractMigration;

class RemoveColorFromComments extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('color');
    }
}
