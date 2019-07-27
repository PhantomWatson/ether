<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\PagesController Test Case
 */
class PagesControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Thoughts' => 'app.Thoughts',
        'Users' => 'app.Users',
        'Comments' => 'app.Comments'
    ];

    /**
     * Test home method
     *
     * @return void
     */
    public function testHome()
    {
        $this->get('/');
        $this->assertResponseOk();
    }

    /**
     * Test about method
     *
     * @return void
     */
    public function testAbout()
    {
        $this->get('/about');
        $this->assertResponseOk();
    }
}
