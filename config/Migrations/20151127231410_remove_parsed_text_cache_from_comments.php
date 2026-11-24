<?php

class RemoveparsedTextCacheFromComments extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('comments');
        $table->removeColumn('parsedTextCache');
    }
}
