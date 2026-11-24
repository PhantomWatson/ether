<?php

class RemoveTimeFromComments extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('time');
    }
}
