<?php
namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;

/**
 * Thoughts Controller
 *
 * @property \App\Model\Table\ThoughtsTable $Thoughts
 */
class ThoughtsController extends AppController
{

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'index',
            'questions',
            'random',
            'recent',
            'refreshFormatting',
            'suggested',
            'word'
        ]);
        $this->loadComponent('RequestHandler');
    }

    /**
     * isAuthorized() method
     *
     * @param array|null $user User array
     * @return bool
     */
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

    /**
     * Renders /thoughts
     *
     * @return void
     */
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
     * @param string|null $id Thought ID
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
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $thought = $this->Thoughts->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['user_id'] = $this->Auth->user('id');
            $thought = $this->Thoughts->patchEntity($thought, $data);
            if ($thought->getErrors()) {
                $this->Flash->error('Please correct the indicated '.__n('error', 'errors', count($thought->getErrors())).' before continuing.');
            } elseif ($this->Thoughts->save($thought)) {
                $event = new Event('Model.Thought.created', $this, ['entity' => $thought]);
                $this->getEventManager()->dispatch($event);
                $this->Flash->success('Your thought has been thunk. Thanks for thinking that thought!');

                return $this->redirect(['action' => 'word', $thought->word]);
            } else {
                $this->Flash->error('There was an error posting that thought. Please try again.');
            }
        } else {
            if ($this->request->getQuery('word') !== null) {
                $thought->set('word', $this->request->getQuery('word'));
            }
        }

        if ($thought->word == null) {
            $suggestedThoughtwords = $this->Thoughts->getSuggestedWords(3);
            $this->set('suggestedThoughtwords', $suggestedThoughtwords);
        }

        $this->set([
            'title_for_layout' => 'Post a New Thought',
            'thought' => $thought
        ]);

        return null;
    }

    /**
     * Edit method
     *
     * @param string|null $id Thought ID
     * @return \Cake\Http\Response|null
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null)
    {
        $thought = $this->Thoughts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $thought = $this->Thoughts->patchEntity($thought, $this->request->getData(), [
                'fieldList' => ['word', 'thought', 'comments_enabled', 'anonymous']
            ]);
            if ($thought->getErrors()) {
                $this->Flash->error('Please correct the indicated '.__n('error', 'errors', count($thought->getErrors())).' before continuing.');
            } elseif ($this->Thoughts->save($thought)) {
                $event = new Event('Model.Thought.updated', $this, ['entity' => $thought]);
                $this->getEventManager()->dispatch($event);
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

        return null;
    }

    /**
     * Delete method
     *
     * @param string|null $id Thought id
     * @return \Cake\Http\Response|null
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
        }

        $this->Flash->error('The thought could not be deleted. Please, try again.');

        return $this->redirect($this->request->referer());
    }

    /**
     * Renders /thoughts/recent
     *
     * @return void
     */
    public function recent()
    {
        $this->paginate['Thoughts']['finder']['recentActivity'] = [];
        $this->viewBuilder()->setLayout('ajax');
        $this->set('recentActivity', $this->paginate('Thoughts'));
    }

    /**
     * Renders /thoughts/word
     *
     * @param string|null $word Thoughtword
     * @return \Cake\Http\Response|null
     */
    public function word($word = null)
    {
        if ($this->request->getData('word')) {
            $word = $this->request->getData('word');

            return $this->redirect(['word' => $word]);
        }
        $word = $this->Thoughts->formatThoughtword($word);
        if ($word === '') {
            throw new BadRequestException('Invalid thoughtword');
        }
        $thoughts = $this->Thoughts->getFromWord($word);
        if (empty($thoughts)) {
            $this->response = $this->response->withStatus(404);
        }
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }

        // Used by the 'add a thought' form
        $thought = $this->Thoughts->newEntity();
        $thought->word = $word;

        $this->set([
            'title_for_layout' => ucwords($word),
            'thought' => $thought,
            'thoughts' => $thoughts,
            'word' => $word,
            'formattingKey' => $this->Thoughts->getPopulatedThoughtwordHash()
        ]);

        return null;
    }

    /**
     * Renders /thoughts/random
     *
     * @return void
     */
    public function random()
    {
        $word = $this->Thoughts->getRandomPopulatedThoughtWord();
        $this->redirect(['action' => 'word', $word]);
    }

    /**
     * Renders /thoughts/refresh-formatting
     *
     * @param int $thoughtId Thought ID
     * @return void
     */
    public function refreshFormatting($thoughtId)
    {
        $this->viewBuilder()->setLayout('json');

        try {
            $thought = $this->Thoughts->get($thoughtId);
        } catch (RecordNotFoundException $e) {
            $this->set([
                '_serialize' => ['result'],
                'result' => [
                    'success' => false,
                    'update' => false
                ]
            ]);
            return;
        }

        $formattingKey = $this->Thoughts->getPopulatedThoughtwordHash();
        if ($formattingKey == $thought->formatting_key) {
            $this->set([
                '_serialize' => ['result'],
                'result' => [
                    'success' => true,
                    'update' => false
                ]
            ]);
            return;
        }

        $formattedThought = $this->Thoughts->formatThought($thought->thought);
        $thought->formatted_thought = $formattedThought;
        $this->Thoughts->save($thought);

        $this->set([
            '_serialize' => ['result'],
            'result' => [
                'success' => true,
                'update' => true,
                'formattedThought' => $formattedThought
            ]
        ]);
    }

    /**
     * Returns a single suggested word from a list of
     * unpopulated thoughtword-candidates
     *
     * @param int $count Number of words returned
     * @return void
     */
    public function suggested($count = 1)
    {
        $suggestedWords = $this->Thoughts->getSuggestedWords($count);
        $this->set('suggestedWords', $suggestedWords);
        $this->set('_serialize', ['suggestedWords']);
    }

    /**
     * Collects and displays questions from thoughts
     *
     * @return void
     */
    public function questions()
    {
        $limit = 100;
        $minLength = 4;
        $maxLength = 50;
        $questions = [];

        $ids = $this->Thoughts->getIdsWithQuestions();
        shuffle($ids);

        foreach ($ids as $id) {
            $thoughts = $this->Thoughts->find()
                ->where(['Thoughts.id' => $id])
                ->select(['id', 'thought', 'anonymous', 'user_id', 'Users.color', 'word'])
                ->contain(['Users'])
                ->limit($limit);
            foreach ($thoughts as $thought) {
                $thoughtsQuestions = $thought->questions;
                shuffle($thoughtsQuestions);
                foreach ($thoughtsQuestions as $question) {
                    // One per thought
                    if (isset($questions[$thought->id])) {
                        continue;
                    }

                    // Must have more than one word
                    if (strpos($question, ' ') === false) {
                        continue;
                    }

                    // Must be the right length
                    if (strlen($question) < $minLength || strlen($question) > $maxLength) {
                        continue;
                    }

                    // Must not start with a conjunction
                    if (stripos($question,'and') === 0 || stripos($question,'but') === 0) {
                        continue;
                    }

                    // Strip out common rhetorical questions
                    $rhetoricalEndings = ['shall we?', ', eh?'];
                    foreach ($rhetoricalEndings as $rhetoricalEnding) {
                        if (stripos($question, $rhetoricalEnding) === strlen($question) - strlen($rhetoricalEnding)) {
                            continue 2;
                        }
                    }

                    $questions[$thought->id] = [
                        'question' => $question,
                        'color' => $thought->anonymous ? null : $thought->user->color,
                        'word' => $thought->word,
                        'thoughtId' => $thought->id
                    ];
                    if (count($questions) >= $limit) {
                        break 3;
                    }
                }
            }
        }
        $this->set([
            'title_for_layout' => 'Questions',
            'questions' => $questions
        ]);
    }
}
