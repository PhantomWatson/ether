<?php

class RemoveTimeSentFromMessages extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('timeSent');
    }
}
