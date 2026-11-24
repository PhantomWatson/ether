<?php

class RemoveTimestampFromUsers extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('timestamp');
    }
}
