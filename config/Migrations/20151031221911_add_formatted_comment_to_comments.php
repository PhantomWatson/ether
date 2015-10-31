<?php

use Phinx\Migration\AbstractMigration;

class AddFormattedCommentToComments extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->addColumn('formatted_comment', 'text', [
            'default' => null,
            'after' => 'comment',
            'null' => true
        ])->addColumn('formatting_key', 'string', [
            'default' => null,
            'after' => 'comment',
            'null' => true,
            'limit' => 32
        ])->update();
    }
}