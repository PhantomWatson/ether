<?php

class RemoveOldFields extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('recipient');
        $table->removeColumn('sender');
    }
}
