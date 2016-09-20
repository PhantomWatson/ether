<?php

use Phinx\Migration\AbstractMigration;

class RemoveParsedTextCacheFromMessages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->removeColumn('parsedTextCache');
    }
}
