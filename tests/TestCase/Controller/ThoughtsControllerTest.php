<?php
namespace App\Test\TestCase\Controller;

use App\Controller\ThoughtsController;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Routing\Router;

/**
 * App\Controller\ThoughtsController Test Case
 */
class ThoughtsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Thoughts' => 'app.thoughts',
        'Users' => 'app.users',
        'Comments' => 'app.comments'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get(['controller' => 'Thoughts', 'action' => 'index']);
        $this->assertResponseOk();
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testWord()
    {
        $this->get(['controller' => 'Thoughts', 'action' => 'word', 'ether']);
        $this->assertResponseOk();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
