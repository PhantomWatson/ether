<?php
use Migrations\AbstractMigration;

class AddDefaultProfile extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $table = $this->table('users');
        $table->changeColumn('profile', 'TEXT_MEDIUM', [
            'default' => '',
        ]);
    }
}
