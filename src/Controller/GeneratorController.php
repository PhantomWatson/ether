<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use MarkovPHP;

class GeneratorController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    public function index()
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $result = $thoughtsTable->generateFromAll(1000, 2, 200);
        $this->set([
            'titleForLayout' => 'Generate a Thought',
            'result' => $result
        ]);
    }
}
