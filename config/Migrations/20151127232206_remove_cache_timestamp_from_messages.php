<?php

class RemoveCacheTimestampFromMessages extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('cacheTimestamp');
    }
}
