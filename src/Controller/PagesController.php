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

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;

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
        $this->loadModel('Thoughts');
        $this->loadModel('Users');
        $this->set([
            'title_for_layout' => 'About Ether',
            'thoughtCount' => $this->Thoughts->getCount(),
            'thinkerCount' => $this->Users->getActiveThinkerCount()
        ]);
        $this->set([
            'title_for_layout' => 'About',
        ]);
    }
}
