<?php

use Phinx\Migration\AbstractMigration;

class RemoveAvailableToSenderFromMessages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('availableToSender');
    }
}
