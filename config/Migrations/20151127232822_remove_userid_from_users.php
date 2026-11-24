<?php

class RemoveUseridFromUsers extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('userid');
    }
}
