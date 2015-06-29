<?php

use Phinx\Migration\AbstractMigration;

class RenameFields extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE `thoughts` CHANGE `parsedTextCache` `formatted_thought` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;');
        $this->execute('ALTER TABLE `thoughts` CHANGE `cacheTimestamp` `formatting_key` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE `thoughts` CHANGE `formatted_thought` `parsedTextCache` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
        $this->execute('ALTER TABLE `thoughts` CHANGE `formatting_key` `cacheTimestamp` INT(11) NOT NULL;');
    }
}
