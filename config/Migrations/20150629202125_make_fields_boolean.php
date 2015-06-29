<?php

use Phinx\Migration\AbstractMigration;

class MakeFieldsBoolean extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE `thoughts` CHANGE `comments_enabled` `comments_enabled` TINYINT(1) NOT NULL DEFAULT \'0\'');
    }

    public function down()
    {
        $this->execute('ALTER TABLE `thoughts` CHANGE `comments_enabled` `comments_enabled` MEDIUMINT(1) NOT NULL DEFAULT \'0\'');
    }
}
