<?php
use Migrations\AbstractMigration;

class AddTtsToThoughts extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('thoughts');
        $table->addColumn('tts', 'string', [
            'default' => null,
            'null' => true,
            'limit' => 10,
            'after' => 'formatting_key',
        ])->update();
    }
}
