<?php

use Phinx\Migration\AbstractMigration;

class RemoveCacheTimestampFromMessages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('cacheTimestamp');
    }
}
