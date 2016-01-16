<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use EtherMarkov\EtherMarkovChain;

class GeneratorController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    public function serverside()
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $result = $thoughtsTable->generateFromAll(1000, 2, 200);
        $result = EtherMarkovChain::trimToNaturalEnding($result);
        $this->set([
            'titleForLayout' => 'Generate a Thought',
            'result' => $result
        ]);
    }

    public function index()
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $thoughts = $thoughtsTable->find('all')
            ->select(['thought'])
            ->toArray();
        $thoughts = Hash::extract($thoughts, '{n}.thought');
        $thoughts = implode(' ', $thoughts);
        $thoughts = str_replace('"', '\"', $thoughts);
        $thoughts = str_replace(["\n", "\r"], " ", $thoughts);
        $thoughts = str_replace('  ', ' ', $thoughts);
        $thoughts = str_replace('  ', ' ', $thoughts);
        $this->set([
            'thoughtsSeed' => $thoughts,
            'titleForLayout' => 'Thought Generator'
        ]);
    }

    public function getSource()
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $thoughts = $thoughtsTable->find('all')
            ->select(['thought'])
            ->toArray();
        $thoughts = Hash::extract($thoughts, '{n}.thought');
        $thoughts = implode(' ', $thoughts);
        $thoughts = str_replace(["\n", "\r"], " ", $thoughts);
        $thoughts = str_replace('  ', ' ', $thoughts);
        $thoughts = str_replace('  ', ' ', $thoughts);
        $this->set('source', $thoughts);
        $this->viewBuilder()->layout('json');
    }
}
