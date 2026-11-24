<?php

class RemoveMarkdownFromComments extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('markdown');
    }
}
