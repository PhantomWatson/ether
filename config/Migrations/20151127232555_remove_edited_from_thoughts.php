<?php

class RemoveEditedFromThoughts extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->removeColumn('edited');
    }
}
