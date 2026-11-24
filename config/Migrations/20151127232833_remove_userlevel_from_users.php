<?php

class RemoveUserlevelFromUsers extends \Migrations\BaseMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('userlevel');
    }
}
