<?php

use Phinx\Migration\AbstractMigration;

class RemovecacheTimestampFromComments extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('cacheTimestamp');
    }
}
