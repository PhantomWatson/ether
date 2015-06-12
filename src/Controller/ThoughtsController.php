<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\BadRequestException;
use Cake\Routing\Router;

/**
 * Thoughts Controller
 *
 * @property \App\Model\Table\ThoughtsTable $Thoughts
 */
class ThoughtsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['recent', 'word', 'index']);
    }

    public function isAuthorized($user = null)
    {
        // Author-only actions
        $authorOnlyActions = ['edit', 'delete'];
        if (in_array($this->request->action, $authorOnlyActions)) {
            $thoughtId = $this->request->pass[0];
            $authorId = $this->Thoughts->getAuthorId($thoughtId);
            return $user['id'] === $authorId;
        }

        return parent::isAuthorized();
    }

    public function index()
    {
        $this->set([
            'title_for_layout' => 'Thoughts',
            'categorized' => $this->Thoughts->getAlphabeticallyGroupedWords()
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id Thought id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($id = null)
    {
        $thought = $this->Thoughts->get($id, [
            'contain' => ['Users', 'Comments']
        ]);
        $this->set('thought', $thought);
    }

    /**
     * Add method
     *
     * @return void
     */
    public function add()
    {
        $thought = $this->Thoughts->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['user_id'] = $this->Auth->user('id');
            $thought = $this->Thoughts->patchEntity($thought, $this->request->data);
            if ($thought->errors()) {
                $this->Flash->error('Please correct the indicated '.__n('error', 'errors', count($thought->errors())).' before continuing.');
            } elseif ($this->Thoughts->save($thought)) {
                $this->Flash->success('Your thought has been thunk. Thanks for thinking that thought!');
                return $this->redirect(['action' => 'word', $thought->word]);
            } else {
                $this->Flash->error('There was an error posting that thought. Please try again.');
            }
        } else {
            if ($this->request->query('word') !== null) {
                $this->request->data['word'] = $this->request->query('word');
            }
        }
        $this->set([
            'title_for_layout' => 'Post a New Thought',
            'thought' => $thought
        ]);
    }

    /**
     * Edit method
     *
     * @param string|null $id Thought id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null)
    {
        $thought = $this->Thoughts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $thought = $this->Thoughts->patchEntity($thought, $this->request->data, [
                'fieldList' => ['word', 'thought', 'comments_enabled', 'anonymous']
            ]);
            if ($thought->errors()) {
                $this->Flash->error('Please correct the indicated '.__n('error', 'errors', count($thought->errors())).' before continuing.');
            } elseif ($this->Thoughts->save($thought)) {
                $this->Flash->success('Your thought has been updated.');
                return $this->redirect(['action' => 'word', $thought->word]);
            } else {
                $this->Flash->error('There was an error updating that thought. Please try again.');
            }
        }
        $this->set([
            'thought' => $thought,
            'title_for_layout' => 'Update Thought'
        ]);
    }

    /**
     * Delete method
     *
     * @param string|null $id Thought id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $thought = $this->Thoughts->get($id);
        $word = $thought->word;
        if ($this->Thoughts->delete($thought)) {
            $this->Flash->success('The thought has been deleted.');
            return $this->redirect(['action' => 'word', $word]);
        } else {
            $this->Flash->error('The thought could not be deleted. Please, try again.');
            return $this->redirect($this->request->referer());
        }
    }

    public function recent($page = 1)
    {
        $this->paginate['Thoughts']['finder']['recentActivity'] = [];
        $this->layout = 'ajax';
        $this->set('recentActivity', $this->paginate('Thoughts'));
    }

    public function word($word = null)
    {
        if (isset($this->request->data['Thought']['word'])) {
            $word = $this->request->data['Thought']['word'];
        }
        $word = $this->Thoughts->formatThoughtword($word);
        if ($word === '') {
            throw new BadRequestException('Invalid thoughtword');
        }
        $thoughts = $this->Thoughts->getFromWord($word);
        if (empty($thoughts)) {
            $this->response->statusCode(404);
        }
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
        }
        $this->set([
            'title_for_layout' => ucwords($word),
            'thoughts' => $thoughts,
            'word' => $word
        ]);
    }

    public function random()
    {
        $word = $this->Thoughts->getRandomPopulatedThoughtWord();
        $this->redirect(['action' => 'word', $word]);
    }
}
