<?php

class RemoveParsedTextCacheFromMessages extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('parsedTextCache');
    }
}
