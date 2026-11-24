<?php

class RemoveMarkdownFromMessages extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('markdown');
    }
}
