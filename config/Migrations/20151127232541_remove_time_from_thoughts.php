<?php

class RemoveTimeFromThoughts extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->removeColumn('time');
    }
}
