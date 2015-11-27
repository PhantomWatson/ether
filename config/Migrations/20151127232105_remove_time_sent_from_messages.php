<?php

use Phinx\Migration\AbstractMigration;

class RemoveTimeSentFromMessages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('timeSent');
    }
}
