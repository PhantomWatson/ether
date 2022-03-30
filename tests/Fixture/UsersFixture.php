<?php
namespace App\Test\Fixture;

/**
 * UsersFixture
 *
 */
class UsersFixture extends AppFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'username' => ['type' => 'string', 'length' => 30, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 40, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'password_version' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'userid' => ['type' => 'string', 'length' => 32, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'userlevel' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'is_admin' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'email' => ['type' => 'string', 'length' => 90, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null, 'fixed' => null],
        'timestamp' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'color' => ['type' => 'string', 'length' => 6, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null, 'fixed' => null],
        'messageNotification' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'profile' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'acceptMessages' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'emailUpdates' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'newMessages' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
'engine' => 'MyISAM', 'collation' => 'latin1_swedish_ci'
        ],
    ];

    public $defaultData = [
        'password' => 'Lorem ipsum dolor sit amet',
        'password_version' => 1,
        'is_admin' => 0,
        'email' => 'Lorem ipsum dolor sit amet',
        'color' => 'Lore',
        'messageNotification' => 1,
        'profile' => 'Lorem ipsum dolor sit amet.',
        'acceptMessages' => 1,
        'emailUpdates' => 1,
        'created' => '2015-01-15 07:20:47',
        'modified' => '2015-01-15 07:20:47'
    ];

    const HAS_SENT_MESSAGES = 1;
    const HAS_RECEIVED_MESSAGES = 2;
    const HAS_COMMENTS = 3;
    const HAS_THOUGHTS = 4;
    const HAS_PROFILE = 5;
    const NO_ACTIVITY_AND_OLD = 6;
    const NO_ACTIVITY_AND_RECENT = 7;

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

    public function init()
    {
        parent::init();

        $this->addRecord(['id' => self::HAS_SENT_MESSAGES]);
        $this->addRecord(['id' => self::HAS_RECEIVED_MESSAGES]);
        $this->addRecord(['id' => self::HAS_COMMENTS]);
        $this->addRecord(['id' => self::HAS_THOUGHTS]);
        $this->addRecord([
            'id' => self::HAS_PROFILE,
            'profile' => 'Lorem ipsum',
        ]);
        $this->addRecord([
            'id' => self::NO_ACTIVITY_AND_OLD,
            'created' => new \DateTime('-7 months'),
            'profile' => '',
        ]);
        $this->addRecord([
            'id' => self::NO_ACTIVITY_AND_RECENT,
            'created' => new \DateTime('-1 day'),
            'profile' => '',
        ]);
    }
}
