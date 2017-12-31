<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\View\Exception\MissingTemplateException;
use Cake\View\View;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
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

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('RequestHandler');
        $this->Auth->allow();
    }

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

    public function about()
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $totalThoughts = $thoughtsTable->find('all')->count();
        $stats['Thoughts'] = number_format($totalThoughts);
        $stats['First thought posted'] = $thoughtsTable->find('all')
            ->select(['created'])
            ->order(['created' => 'ASC'])
            ->first()
            ->created
            ->format('M d, Y');
        $thoughtsCommentsEnabled = $thoughtsTable->find('all')
            ->where(['comments_enabled' => 1])
            ->count();
        $stats['Thoughts with comments enabled'] = round(($thoughtsCommentsEnabled / $totalThoughts) * 100, 2).'%';
        $mostPopularWord = $thoughtsTable->find('all')
            ->select([
                'word',
                'count' => $thoughtsTable->find()->func()->count('*')
            ])
            ->group('word')
            ->order(['count' => 'DESC'])
            ->first();
        $url = Router::url(['controller' => 'thoughts', 'action' => 'word', $mostPopularWord->word]);
        $stats['Most thought-about thoughtword'] = '<a href="'.$url.'">'.$mostPopularWord->word.'</a> ('.number_format($mostPopularWord->count).' thoughts)';

        $commentsTable = TableRegistry::get('Comments');
        $totalComments = $commentsTable->find('all')->count();
        $stats['Comments'] = number_format($totalComments);

        $usersTable = TableRegistry::get('Users');
        $totalThinkers = $usersTable->find('all')->count();
        $stats['Thinkers'] = $totalThinkers;
        $stats['Thinkers who have posted thoughts'] = round(($usersTable->getActiveThinkerCount() / $totalThinkers) * 100, 2).'%';
        $usersMessagesEnabled = $usersTable->find('all')
            ->where(['acceptMessages' => 1])
            ->count();
        $stats['Thinkers who accept messages'] = round(($usersMessagesEnabled / $totalThinkers) * 100, 2).'%';

        $this->set([
            'title_for_layout' => 'About Ether',
            'thoughtCount' => $totalThoughts,
            'thinkerCount' => $usersTable->getActiveThinkerCount(),
            'stats' => $stats
        ]);
    }

    public function markdown()
    {
        $this->set(['title_for_layout' => 'Markdown']);
    }

    public function terms()
    {
        $this->set(['titleForLayout' => 'Terms of Use']);
    }

    public function privacy()
    {
        $this->set(['titleForLayout' => 'Privacy Policy']);
    }

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
        $this->viewBuilder()->layout('ajax');
        $this->response->statusCode(404);
    }

    public function colorNames()
    {
        $View = new View();
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::get('Users');
        $colors = $usersTable->getColorsWithThoughts();
        $Color = new \App\Color\Color();

        $retval = [];
        foreach ($colors as $section => $sectionColors) {
            foreach ($sectionColors as $color => $count) {
                $closest = $Color->getClosestXkcdColor($color);
                $retval[$color] = $closest ? $closest['name'] : null;
            }
        }
        $this->set([
            'colors' => $retval,
            'title_for_layout' => 'Color Names'
        ]);
    }
}
