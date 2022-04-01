<?php
namespace App\Controller;

use App\Color\Color;
use App\Model\Entity\Thought;
use App\Model\Table\ThoughtsTable;
use App\Model\Table\UsersTable;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Exception;

/**
 * @property ThoughtsTable $Thoughts
 * @method Thought[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class PagesController extends AppController
{
    public $helpers = [
        'Paginator' => []
    ];

    public $paginate = [
        'finder' => [
            'recentActivity' => []
        ]
    ];

    /**
     * Initialize method
     *
     * @throws Exception
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('RequestHandler');
        $this->Auth->allow();
    }

    /**
     * Site homepage
     *
     * @return void
     */
    public function home()
    {
        $this->loadModel('Thoughts');
        $randomThought = $this->Thoughts->getRandomThought();
        $randomThought = $this->Thoughts->excerpt($randomThought);
        $this->set([
            'recentActivity' => $this->paginate($this->Thoughts),
            'cloud' => $this->Thoughts->getCloud(),
            'randomThought' => $randomThought
        ]);
    }

    /**
     * About page
     *
     * @return void
     */
    public function about()
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $stats = $this->getStats();

        $this->set([
            'title_for_layout' => 'About Ether',
            'thinkerCount' => $usersTable->getActiveThinkerCount(),
            'stats' => $stats
        ]);
    }

    /**
     * @return array
     */
    private function getStats(): array
    {
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $totalThoughts = $thoughtsTable->find('all')->count();
        $stats['Thoughts'] = number_format($totalThoughts);

        /** @var Thought $firstThought */
        $firstThought = $thoughtsTable->find('all')
            ->select(['created'])
            ->order(['created' => 'ASC'])
            ->first();
        $stats['First thought posted'] = $firstThought
            ->created
            ->format('M d, Y');

        $thoughtsCommentsEnabled = $thoughtsTable->find('all')
            ->where(['comments_enabled' => 1])
            ->count();
        $stats['Thoughts with comments enabled'] = $this->getThinkerPercent(
            $thoughtsCommentsEnabled,
            $totalThoughts
        );

        /** @var Thought $mostPopularWord */
        $mostPopularWord = $thoughtsTable->find('all')
            ->select([
                'word',
                'count' => $thoughtsTable->find()->func()->count('*')
            ])
            ->group('word')
            ->order(['count' => 'DESC'])
            ->first();
        $url = Router::url(['controller' => 'Thoughts', 'action' => 'word', $mostPopularWord->word]);
        $stats['Most thought-about thoughtword'] = sprintf(
            '<a href="%s">%s</a> (%s thoughts)',
            $url,
            $mostPopularWord->word,
            number_format($mostPopularWord->count)
        );

        $commentsTable = TableRegistry::getTableLocator()->get('Comments');
        $totalComments = $commentsTable->find('all')->count();
        $stats['Comments'] = number_format($totalComments);

        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $totalThinkers = $usersTable->find('all')->count();
        $stats['Thinkers'] = $totalThinkers;
        $stats['Thinkers who have posted thoughts'] = $this->getThinkerPercent(
            $usersTable->getActiveThinkerCount(),
            $totalThinkers
        );
        $stats['Thinkers who have only posted thoughts'] = $this->getThinkerPercent(
            $usersTable->getOnlyPostedThoughtsCount(),
            $totalThinkers
        );
        $stats['Thinkers who have only posted comments'] = $this->getThinkerPercent(
            $usersTable->getOnlyPostedCommentsCount(),
            $totalThinkers
        );
        $stats['Thinkers who have only sent messages'] = $this->getThinkerPercent(
            $usersTable->getOnlySentMessagesCount(),
            $totalThinkers
        );
        $usersMessagesEnabled = $usersTable->find('all')
            ->where(['acceptMessages' => 1])
            ->count();
        $stats['Thinkers who accept messages'] = $this->getThinkerPercent(
            $usersMessagesEnabled,
            $totalThinkers
        );

        return $stats;
    }

    /**
     * @param int $count
     * @param int $total
     * @return string
     */
    private function getThinkerPercent($count, $total)
    {
        return round(($count / $total) * 100, 2) . '%';
    }

    /**
     * Page explaining how markdown works
     *
     * @return void
     */
    public function markdown()
    {
        $this->set(['title_for_layout' => 'Markdown']);
    }

    /**
     * Terms and conditions page
     *
     * @return void
     */
    public function terms()
    {
        $this->set(['titleForLayout' => 'Terms of Use']);
    }

    /**
     * Privacy policy page
     *
     * @return void
     */
    public function privacy()
    {
        $this->set(['titleForLayout' => 'Privacy Policy']);
    }

    /**
     * Contact page
     *
     * @return void
     */
    public function contact()
    {
        $this->set(['titleForLayout' => 'Contact']);
    }

    /**
     * A simple 404 page to render for bot requests
     *
     * @return void
     */
    public function botCatcher()
    {
        $this->viewBuilder()->setLayout('ajax');
        $this->response = $this->response->withStatus(404);
    }

    /**
     * A page for displaying each color's name
     *
     * @return void
     */
    public function colorNames()
    {
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $colors = $usersTable->getColorsWithThoughts();
        $Color = new Color();
        $hexCodes = [];
        foreach ($colors as $section => $sectionColors) {
            foreach ($sectionColors as $color => $count) {
                $hexCodes[] = $color;
            }
        }

        $this->set([
            'colors' => $Color->getClosestXkcdColors($hexCodes),
            'title_for_layout' => 'Color Names'
        ]);
    }
}
