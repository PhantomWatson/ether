<?php

class RemoveAvailableToRecipientFromMessages extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('availableToRecipient');
    }
}
