<?php

use Phinx\Migration\AbstractMigration;

class AddMarkdownFlag extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->addColumn('markdown', 'boolean', ['default' => false, 'after' => 'thought'])->update();

        $table = $this->table('comments');
        $table->addColumn('markdown', 'boolean', ['default' => false, 'after' => 'comment'])->update();

        $table = $this->table('messages');
        $table->addColumn('markdown', 'boolean', ['default' => false, 'after' => 'message'])->update();

        $table = $this->table('users');
        $table->addColumn('markdown', 'boolean', ['default' => false, 'after' => 'profile'])->update();
    }
}
