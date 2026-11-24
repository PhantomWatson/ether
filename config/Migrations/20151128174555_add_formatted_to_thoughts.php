<?php

class AddFormattedToThoughts extends \Migrations\BaseMigration
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
