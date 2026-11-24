<?php

class RemoveColorFromThoughts extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->removeColumn('color');
    }
}
