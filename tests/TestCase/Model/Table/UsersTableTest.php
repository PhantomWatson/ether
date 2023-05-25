<?php
namespace App\Test\TestCase\Model\Table;

use App\Test\Fixture\UsersFixture;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Users' => 'app.Users',
        'Comments' => 'app.Comments',
        'Thoughts' => 'app.Thoughts',
        'Messages' => 'app.Messages',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::exists('Users') ? [] : ['className' => 'App\Model\Table\UsersTable'];
        $this->Users = TableRegistry::get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testGetInactiveUsersToPrune()
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $users = $usersTable->getInactiveUsersToPrune();
        $userIds = array_map(function ($user) {
            return $user->id;
        }, $users->toArray());

        $includedUsers = [UsersFixture::NO_ACTIVITY_AND_OLD];
        foreach ($includedUsers as $includedUser) {
            $this->assertContains($includedUser, $userIds, 'Expected user was not included');
        }

        $excludedUsers = [
            UsersFixture::HAS_SENT_MESSAGES,
            UsersFixture::HAS_RECEIVED_MESSAGES,
            UsersFixture::HAS_COMMENTS,
            UsersFixture::HAS_THOUGHTS,
            UsersFixture::HAS_PROFILE,
            UsersFixture::NO_ACTIVITY_AND_RECENT,
        ];
        foreach ($excludedUsers as $excludedUser) {
            $this->assertNotContains($excludedUser, $userIds, 'User was included in error');
        }
    }
}
