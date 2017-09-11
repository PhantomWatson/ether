<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Routing\Router;
use EtherMarkov\EtherMarkovChain;

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
                $event = new Event('Model.Thought.created', $this, ['entity' => $thought]);
                $this->eventManager()->dispatch($event);
                $this->Flash->success('Your thought has been thunk. Thanks for thinking that thought!');
                return $this->redirect(['action' => 'word', $thought->word]);
            } else {
                $this->Flash->error('There was an error posting that thought. Please try again.');
            }
        } else {
            if ($this->request->query('word') !== null) {
                $thought->set('word', $this->request->query('word'));
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
                $event = new Event('Model.Thought.updated', $this, ['entity' => $thought]);
                $this->eventManager()->dispatch($event);
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
        $this->viewBuilder()->layout('ajax');
        $this->set('recentActivity', $this->paginate('Thoughts'));
    }

    public function word($word = null)
    {
        if (isset($this->request->data['word'])) {
            $word = $this->request->data['word'];
            $this->redirect(['word' => $word]);
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
            $this->viewBuilder()->layout('ajax');
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
    }

    public function random()
    {
        $word = $this->Thoughts->getRandomPopulatedThoughtWord();
        $this->redirect(['action' => 'word', $word]);
    }

    public function refreshFormatting($thoughtId)
    {
        $this->viewBuilder()->layout('json');

        try {
            $thought = $this->Thoughts->get($thoughtId);
        } catch (RecordNotFoundException $e) {
            $this->set([
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
        $passes = 10;
        $minLength = 4;
        $maxLength = 50;
        $questions = [];
        for ($n = 0; $n < $passes; $n++) {
            $thoughts = $this->Thoughts->find('withQuestions')
                ->select(['id', 'thought', 'anonymous', 'user_id', 'Users.color', 'word'])
                ->contain(['Users'])
                ->limit($limit);
            foreach ($thoughts as $thought) {
                foreach ($thought->questions as $question) {
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

                    $questions[] = [
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
