<?php

use Phinx\Migration\AbstractMigration;

class RemoveparsedTextCacheFromComments extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('parsedTextCache');
    }
}
