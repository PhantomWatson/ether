<?php

class RemovecacheTimestampFromComments extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('cacheTimestamp');
    }
}
