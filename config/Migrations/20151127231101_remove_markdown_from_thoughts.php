<?php

class RemoveMarkdownFromThoughts extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->removeColumn('markdown');
    }
}
