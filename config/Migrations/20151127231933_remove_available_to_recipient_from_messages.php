<?php

use Phinx\Migration\AbstractMigration;

class RemoveAvailableToRecipientFromMessages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('availableToRecipient');
    }
}
