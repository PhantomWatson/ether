<?php

class RemoveStatusFromMessages extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('status');
    }
}
