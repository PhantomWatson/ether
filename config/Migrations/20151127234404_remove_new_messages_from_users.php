<?php

class RemoveNewMessagesFromUsers extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('newMessages');
    }
}
