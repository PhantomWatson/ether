<?php

use Phinx\Migration\AbstractMigration;

class RemoveMarkdownFromMessages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('markdown');
    }
}
