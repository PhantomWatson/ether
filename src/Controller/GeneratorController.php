<?php
namespace App\Controller;

use App\Model\Table\ThoughtsTable;
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
        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $result = $thoughtsTable->generateFromAll(1000, 2, 200);
        $result = EtherMarkovChain::trimToNaturalEnding($result);
        $this->set([
            'titleForLayout' => 'Generate a Thought',
            'result' => $result
        ]);
    }

    public function index()
    {
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
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
            'title_for_layout' => 'Thought Generator'
        ]);
    }

    public function getSource()
    {
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $thoughts = $thoughtsTable->find('all')
            ->select(['thought'])
            ->toArray();
        $thoughts = Hash::extract($thoughts, '{n}.thought');
        $thoughts = implode(' ', $thoughts);
        $thoughts = str_replace(["\n", "\r"], " ", $thoughts);
        $thoughts = str_replace('  ', ' ', $thoughts);
        $thoughts = str_replace('  ', ' ', $thoughts);
        $this->set('source', $thoughts);
        $this->viewBuilder()->setLayout('json');
    }
}
