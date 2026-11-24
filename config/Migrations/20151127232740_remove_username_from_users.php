<?php

class RemoveUsernameFromUsers extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('username');
    }
}
