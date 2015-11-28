<?php

use Phinx\Migration\AbstractMigration;

class AddFormattedToThoughts extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('thoughts');
        $table->addColumn('formatted', 'datetime', [
            'default' => null,
            'null' => true
        ])->update();
    }
}
