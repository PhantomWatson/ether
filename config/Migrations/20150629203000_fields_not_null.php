<?php

use Phinx\Migration\AbstractMigration;

class FieldsNotNull extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE `messages` CHANGE `recipient_id` `recipient_id` INT(11) NOT NULL;');
        $this->execute('ALTER TABLE `messages` CHANGE `sender_id` `sender_id` INT(11) NOT NULL;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE `messages` CHANGE `recipient_id` `recipient_id` INT(11) NULL;');
        $this->execute('ALTER TABLE `messages` CHANGE `sender_id` `sender_id` INT(11) NULL;');
    }
}
