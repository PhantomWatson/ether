<?php

use Phinx\Migration\AbstractMigration;

class RemoveMarkdownFromComments extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('markdown');
    }
}
