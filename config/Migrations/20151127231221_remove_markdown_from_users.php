<?php

use Phinx\Migration\AbstractMigration;

class RemoveMarkdownFromUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('markdown');
    }
}
