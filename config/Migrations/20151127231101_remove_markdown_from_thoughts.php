<?php

use Phinx\Migration\AbstractMigration;

class RemoveMarkdownFromThoughts extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->removeColumn('markdown');
    }
}
