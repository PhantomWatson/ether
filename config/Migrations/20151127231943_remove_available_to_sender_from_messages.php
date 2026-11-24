<?php

class RemoveAvailableToSenderFromMessages extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('availableToSender');
    }
}
