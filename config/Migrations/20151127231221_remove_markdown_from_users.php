<?php

class RemoveMarkdownFromUsers extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('markdown');
    }
}
