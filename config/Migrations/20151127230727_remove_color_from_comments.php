<?php

class RemoveColorFromComments extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('color');
    }
}
