<?php
namespace App\Controller;

use App\Model\Table\ThoughtsTable;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use EtherMarkov\EtherMarkovChain;
use Exception;

class GeneratorController extends AppController
{
    /**
     * Initialize method
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    /**
     * Generates a thought server-side and renders it
     *
     * @return void
     */
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

    /**
     * Thought generator index page
     *
     * @return void
     */
    public function index()
    {
        $thoughts = $this->getGeneratorSourceData();
        $this->set([
            'thoughtsSeed' => $thoughts,
            'title_for_layout' => 'Thought Generator'
        ]);
    }

    /**
     * Returns generator source data as a JSON object
     *
     * @return void
     */
    public function getSource()
    {
        $thoughts = $this->getGeneratorSourceData();
        $this->set('source', $thoughts);
        $this->viewBuilder()->setLayout('json');
    }

    /**
     * Returns a string containing all thoughts concatenated together
     *
     * @return string
     */
    private function getGeneratorSourceData()
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

        return $thoughts;
    }
}
