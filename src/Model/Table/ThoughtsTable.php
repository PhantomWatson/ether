<?php
namespace App\Model\Table;

use App\Model\Entity\Thought;
use Cake\Cache\Cache;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\Log;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use EtherMarkov\EtherMarkovChain;
use League\CommonMark\CommonMarkConverter;
use League\HTMLToMarkdown\HtmlConverter;

/**
 * Thoughts Model
 *
 * @method Query findByUserIdAndThought($userId, $thought)
 * @property UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property CommentsTable|\Cake\ORM\Association\HasMany $Comments
 * @method Thought get($primaryKey, $options = [])
 * @method Thought newEntity($data = null, array $options = [])
 * @method Thought[] newEntities(array $data, array $options = [])
 * @method Thought|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method Thought patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method Thought[] patchEntities($entities, array $data, array $options = [])
 * @method Thought findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Gourmet\CommonMark\Model\Behavior\CommonMarkBehavior
 */
class ThoughtsTable extends Table
{

    public $maxThoughtwordLength = 30;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('thoughts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'thought_id'
        ]);
        $this->addBehavior('Gourmet/CommonMark.CommonMark');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->scalar('id')
            ->numeric('id');

        $validator
            ->scalar('user_id')
            ->numeric('user_id');

        $validator
            ->scalar('word')
            ->requirePresence('word', 'create')
            ->allowEmptyString('word', 'Thoughtword required', false);

        $validator
            ->scalar('thought')
            ->requirePresence('thought', 'create')
            ->minLength('thought', 20, 'That thought is way too short! Please enter at least 20 characters.');

        $validator
            ->scalar('comments_enabled')
            ->boolean('comments_enabled');

        $validator
            ->scalar('anonymous')
            ->boolean('anonymous');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * Returns an alphabetized list of all unique populated thoughtwords
     *
     * @return array
     */
    public function getWords()
    {
        return Cache::remember('populatedThoughtwords', function () {
            $populatedThoughtwords = $this
                ->find('all')
                ->select(['word'])
                ->distinct(['word'])
                ->order(['word' => 'ASC'])
                ->extract('word')
                ->toArray();
            $populatedThoughtwordHash = md5(serialize($populatedThoughtwords));
            Cache::write('populatedThoughtwordHash', $populatedThoughtwordHash);

            return $populatedThoughtwords;
        });
    }

    public function getPopulatedThoughtwordHash()
    {
        return Cache::remember('populatedThoughtwordHash', function () {
            $populatedThoughtwords = $this->getWords();

            return md5(serialize($populatedThoughtwords));
        });
    }

    /**
     * Returns a list of the 300 most-populated thoughtwords and their thought counts
     *
     * @return array
     */
    public function getTopCloud()
    {
        return $this->getCloud(300);
    }

    /**
     * Returns a list of all thoughtwords and their thought counts
     *
     * @param int|bool $limit Word limit
     * @return array
     */
    public function getCloud($limit = false)
    {
        return Cache::remember('thoughtwordCloud', function () use ($limit) {
            $query = $this->find('list', [
                    'keyField' => 'word',
                    'valueField' => 'count'
                ])
                ->select([
                    'word',
                    'count' => $this->find()->func()->count('*')
                ])
                ->group('word')
                ->order(['count' => 'DESC']);
            if ($limit) {
                $query->limit($limit);
            }
            $result = $query->toArray();
            ksort($result);

            return $result;
        }, 'long');
    }

    /**
     * Returns a count of unique populated thoughtwords
     *
     * @return int
     */
    public function getWordCount()
    {
        return $this
            ->find('all')
            ->select(['word'])
            ->distinct(['word'])
            ->count();
    }

    /**
     * Returns a random populated thoughtword
     *
     * @return string
     */
    public function getRandomPopulatedThoughtWord()
    {
        /** @var Thought $thought */
        $thought = $this->find('all')
            ->select(['word'])
            ->order('RAND()')
            ->first();

        return $thought->word;
    }

    /**
     * Returns a random thought
     *
     * @return Entity
     */
    public function getRandomThought()
    {
        $allThoughtIds = $this->getAllIds();
        $key = array_rand($allThoughtIds);
        $thoughtId = $allThoughtIds[$key];

        /** @var Thought $thought */
        $thought = $this->find('all')
            ->select(['id', 'word', 'thought', 'formatted_thought', 'anonymous', 'formatting_key'])
            ->where(['Thoughts.id' => $thoughtId])
            ->contain([
                'Users' => function ($q) {
                    /** @var Query $q */

                    return $q->select(['id', 'color']);
                }
            ])
            ->first();

        // Generate or refresh formatted_thought if necessary and save result
        if (empty($thought->formatted_thought) || empty($thought->formatting_key) || $thought->formatting_key != $this->getPopulatedThoughtwordHash()) {
            $thought->formatted_thought = $this->formatThought($thought->thought);
            $this->save($thought);
        }

        return $thought;
    }

    /**
     * Returns the beginning 300 characters of a thought for the front
     * page "random thought", with all tags but bold and italics removed.
     *
     * @param Entity $thought
     * @return Entity $thought
     */
    public function excerpt($thought)
    {
        $t = $thought->formatted_thought;

        // Replace breaks with spaces to avoid "First line.Second line."
        $t = str_replace(['<p>', '</p>'], '', $t);
        $t = str_replace(['<br />', '<br>'], ' ', $t);

        $allowedTags = '<i><b><em><strong>';
        $t = strip_tags($t, $allowedTags);

        $t = Text::truncate($t, 300, [
            'html' => true,
            'exact' => false
        ]);
        $t = trim($t);

        $thought->formatted_thought = $t;

        return $thought;
    }

    /**
     * Returns an array of ['first letter' => [words beginning with that letter], ...]
     *
     * @return array
     */
    public function getAlphabeticallyGroupedWords()
    {
        $words = $this->getWords();
        $categorized = [];
        foreach ($words as $word) {
            $first_letter = substr($word, 0, 1);
            if (is_numeric($first_letter)) {
                $categorized['#'][] = $word;
            } else {
                $categorized[$first_letter][] = $word;
            }
        }
        ksort($categorized);

        return $categorized;
    }

    /**
     * Used to get paginated thoughts and comments combined
     *
     * @param Query $query
     * @param array $options
     * @return Query
     * @throws BadRequestException
     */
    public function findRecentActivity(Query $query, array $options)
    {
        $combinedQuery = $this->getThoughtsAndComments();
        $limit = 10;
        $offset = $query->clause('offset');
        $direction = isset($_GET['direction']) ? strtolower($_GET['direction']) : 'desc';
        if (! in_array($direction, ['asc', 'desc'])) {
            throw new BadRequestException('Invalid sorting direction');
        }
        $combinedQuery->epilog("ORDER BY created $direction LIMIT $limit OFFSET $offset");
        $combinedQuery->counter(function ($query) {
            $comments = TableRegistry::getTableLocator()->get('Comments');

            return $comments->find('all')->count() + $this->find('all')->count();
        });

        return $combinedQuery;
    }

    /**
     * Returns a query the selects thoughts and associated authors and comments
     *
     * @return Query
     */
    public function getThoughtsAndComments()
    {
        $thoughts = TableRegistry::getTableLocator()->get('Thoughts');
        $thoughtsQuery = $thoughts->find('all');
        $thoughtsQuery
            ->select([
                'created' => 'Thoughts.created',
                'thought_id' => 'Thoughts.id',
                'thought_word' => 'Thoughts.word',
                'thought_anonymous' => 'Thoughts.anonymous',
                'comment_id' => 0
            ])
            ->contain([
                'Users' => [
                    'fields' => ['id', 'color']
                ]
            ]);

        $comments = TableRegistry::getTableLocator()->get('Comments');
        $commentsQuery = $comments
            ->find('all')
            ->select([
                'created' => 'Comments.created',
                'thought_id' => 'Thoughts.id',
                'thought_word' => 'Thoughts.word',
                'thought_anonymous' => 'Thoughts.anonymous',
                'comment_id' => 'Comments.id'
            ])
            ->contain([
                'Users' => [
                    'fields' => ['id', 'color']
                ]
            ])
            ->join([
                'table' => 'thoughts',
                'alias' => 'Thoughts',
                'conditions' => 'Comments.thought_id = Thoughts.id'
            ]);

        return $thoughtsQuery->unionAll($commentsQuery);
    }

    /**
     * Converts $word into a valid thoughtword (alphanumeric, lowercase, no spaces, max length enforced)
     *
     * @param string $word
     * @return string
     */
    public function formatThoughtword($word)
    {
        $word = preg_replace('/[^a-zA-Z0-9]/', '', $word);
        if (strlen($word) > $this->maxThoughtwordLength) {
            $word = substr($word, 0, $this->maxThoughtwordLength);
        }
        return strtolower($word);
    }

    /**
     * Checks to see if the thought in $this->request->data is already in the database
     *
     * @param int $userId User ID
     * @param string $thought Thought text
     * @return int|boolean Either the ID of the existing thought or FALSE
     */
    public function isDuplicate($userId, $thought)
    {
        $results = $this
            ->findByUserIdAndThought($userId, $thought)
            ->select(['id'])
            ->order(['Thought.created' => 'DESC'])
            ->first()
            ->toArray();
        return isset($results['Thought']['id']) ? $results['Thought']['id'] : false;
    }

    /**
     * @param string $word
     * @return \App\Model\Entity\Thought[]
     */
    public function getFromWord($word)
    {
        return $this->find('all')
            ->select([
                'id',
                'user_id',
                'word',
                'thought',
                'comments_enabled',
                'formatted_thought',
                'formatting_key',
                'anonymous',
                'created',
                'modified'])
            ->where(['word' => $word])
            ->order(['Thoughts.created' => 'DESC'])
            ->contain([
                'Users' => function ($q) {
                    /** @var Query $q */

                    return $q->select(['id', 'color']);
                },
                'Comments' => function ($q) {
                    /** @var Query $q */

                    return $q
                        ->select([
                            'id',
                            'thought_id',
                            'user_id',
                            'comment',
                            'formatted_comment',
                            'formatting_key',
                            'anonymous'
                        ])
                        ->contain([
                            'Users' => function ($q) {
                                /** @var Query $q */

                                return $q->select(['id', 'color']);
                            }
                        ])
                        ->order(['Comments.created' => 'ASC']);
                },
            ])
            ->toArray();
    }

    /**
     * Convert the user-entered contents of a thought to what will
     * be displayed (with Markdown to HTML, thoughtwords linked, etc.)
     *
     * @param string $thought
     * @return string
     */
    public function formatThought($thought)
    {
        // Remove all HTML added by the user
        $thought = $this->stripTags($thought, true);

        // Convert Markdown to HTML, then strip all tags not whitelisted
        $thought = $this->parseMarkdown($thought);
        $thought = $this->stripTags($thought);

        $thought = $this->linkThoughtwords($thought);
        $thought = $this->addWordBreaks($thought);
        return $thought;
    }

    public function parseMarkdown($input)
    {
        $converter = new CommonMarkConverter();
        return $converter->convertToHtml($input);
    }

    public function stripTags($input, $allTags = false)
    {
        $allowedTags = '<i><b><em><strong><ul><ol><li><p><br><wbr><blockquote>';
        if ($allTags) {
            return strip_tags($input);
        }
        return strip_tags($input, $allowedTags);
    }

    /**
     * Returns $input with links around every thoughtword
     *
     * @param string $input
     * @return string
     */
    public function linkThoughtwords($input)
    {
        $thoughtwords = $this->getWords();
        $trimPattern = "/(^[^a-zA-Z0-9]+|[^a-zA-Z0-9]+$)/"; // Pattern used to isolate leading/trailing non-alphanumeric characters
        $nonalphanumericPattern = '/[^a-zA-Z0-9]/';
        $tags = ['i', 'b', 'em', 'strong', 'p', 'ul', 'ol', 'li', 'br', 'wbr', 'blockquote'];
        $selfClosingTags = ['br', 'wbr'];
        $whitespaceAndTagsPattern = '/( |\n|\r';
        foreach ($tags as $tag) {
            $whitespaceAndTagsPattern .= '|<' . $tag . '>|<\/' . $tag . '>';
        }
        foreach ($selfClosingTags as $tag) {
            $whitespaceAndTagsPattern .= '|<' . $tag . ' \/>';
        }
        $whitespaceAndTagsPattern .= ')/';
        $entirelyNonalphanumericPattern = '/^[^a-zA-Z0-9]+$/';
        $formattedText = '';
        $textBroken = preg_split($whitespaceAndTagsPattern, $input, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($textBroken as $n => $textChunk) {
            // Chunk is a delimiter
            if (preg_match($whitespaceAndTagsPattern, $textChunk)) {
                $formattedText .= $textChunk;
                continue;
            }

            // Lowercase, alphanumeric-only version of chunk
            $word = strtolower(preg_replace($nonalphanumericPattern, "", $textChunk));

            // Chunk is ineligible for linking
            if (! in_array($word, $thoughtwords)) {
                $formattedText .= $textChunk;
                continue;
            }

            $url = Router::url(['controller' => 'Thoughts', 'action' => 'word', $word, 'plugin' => false]);

            // Thoughtword is intact inside chunk
            // (So leave leading/trailing non-alphanumeric character out of link)
            $stripos = stripos($textChunk, $word);
            if ($stripos !== false) {
                $unformattedWord = substr($textChunk, $stripos, strlen($word));
                $formattedText .= str_replace(
                    $unformattedWord,
                    '<a href="'.$url.'" class="thoughtword">'.$unformattedWord.'</a>',
                    $textChunk
                );
                continue;
            }

            // Thoughtword is broken up (such as the word 'funhouse' is broken up in 'fun-house')
            // (So include intervening non-alphanumeric characters in link, but not leading/trailing)
            $splitChunk = preg_split($trimPattern, $textChunk, -1, PREG_SPLIT_DELIM_CAPTURE);

            // Removes empty subchunks
            foreach ($splitChunk as $key => $subchunk) {
                if ($subchunk == '') {
                    unset($splitChunk[$key]);
                }
            }
            $splitChunk = array_values($splitChunk); // Resets keys
            $lastKey = count($splitChunk) - 1;

            // If the chunk of text LEADS with non-alphanumeric characters, don't include them in the link.
            $firstChunk = $splitChunk[0];
            if ($leadingCharacters = preg_match($entirelyNonalphanumericPattern, $firstChunk)) {
                $formattedText .= $firstChunk;
                array_shift($splitChunk);
                $lastKey--;
            }

            // If the chunk of text ENDS with non-alphanumeric characters, don't include them in the link.
            $lastChunk = $splitChunk[$lastKey];
            if ($trailingCharacters = preg_match($entirelyNonalphanumericPattern, $lastChunk)) {
                array_pop($splitChunk);
            }

            $linkedChunk = ($leadingCharacters || $trailingCharacters) ? implode("", $splitChunk) : $textChunk;
            $formattedText .= '<a href="'.$url.'" class="thoughtword">'.$linkedChunk.'</a>';

            if ($trailingCharacters) {
                $formattedText .= $lastChunk;
            }
        }
        return $formattedText;
    }

    public function addWordBreaks($input)
    {
        $whitespaceAndTagsPattern = "/( |\n|\r|<[^>]*>)/";
        $output = '';
        $textBroken = preg_split($whitespaceAndTagsPattern, $input, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($textBroken as $n => $textChunk) {
            if ($textChunk == '') {
                continue;
            } elseif ($textChunk[0] == '<') {
                $output .= $textChunk;
            } elseif (strlen($textChunk) > $this->maxThoughtwordLength) {
                $output .= chunk_split($textChunk, $this->maxThoughtwordLength, "<wbr />");
            } else {
                $output .= $textChunk;
            }
        }

        return $output;
    }

    public function afterDelete($event, $entity, $options = [])
    {
        $event = new Event('Model.Thought.deleted', $this, compact('entity', 'options'));
        $this->getEventManager()->dispatch($event);
    }

    public function getAuthorId($thoughtId)
    {
        return $this->get($thoughtId, [
            'fields' => ['user_id']
        ])->user_id;
    }

    public function getCount()
    {
        return $this->find('all')->count();
    }

    public function getPopulation($word)
    {
        return $this->find('all')->where(['word' => $word])->count();
    }

    /**
     * Finds a batch of thoughts with out-of-date formatting
     * (e.g. because of newly-populated thoughtwords)
     *
     * @param int $limit
     * @return Query
     */
    public function getThoughtsForReformatting($limit = null)
    {
        $populatedThoughtwordHash = $this->getPopulatedThoughtwordHash();

        return $this
            ->find('all')
            ->select(['id', 'thought'])
            ->where([
                'OR' => [
                    function (QueryExpression $exp) {
                        return $exp->isNull('formatting_key');
                    },
                    'formatting_key IS NOT' => $populatedThoughtwordHash
                ]
            ])
            ->limit($limit)
            ->order(['created' => 'DESC']);
    }

    /**
     * Collects a batch of $limit thoughts with out-of-date formatting
     * and updates them.
     *
     * @param int|null $limit
     */
    public function reformatStaleThoughts($limit = null)
    {
        $query = $this->getThoughtsForReformatting($limit);
        if ($query->count() === 0) {
            Log::write('info', 'No stale thoughts found.');
            return;
        }

        foreach ($query as $thought) {
            $thought->formatted_thought = $this->formatThought($thought->thought);
            // Thoughts.formatting_key automatically set by Thought::_setFormattedThought()
            $this->save($thought);
            Log::write('info', 'Refreshed formatting for thought '.$thought->id);
        }
    }

    /**
     * Removes slashes that were a leftover of the anti-injection-attack strategy of the olllllld Ether
     *
     * @return void
     */
    public function overhaulStripSlashes()
    {
        $thoughts = $this->find('all')
            ->select(['id', 'thought'])
            ->where(['thought LIKE' => '%\\\\%'])
            ->order(['id' => 'ASC']);
        foreach ($thoughts as $thought) {
            echo $thought->thought;
            $fixed = stripslashes($thought->thought);
            $thought->thought = $fixed;
            $this->save($thought);
            echo " => $fixed<br />";
        }
    }

    public function overhaulToMarkdown()
    {
        $field = 'thought';
        $results = $this->find('all')
            ->select(['id', $field])
            ->where([
                "$field LIKE" => '%<%',
                'markdown' => false
            ])
            ->order(['id' => 'ASC']);
        if ($results->count() == 0) {
            echo "No {$field}s to convert";
        }
        foreach ($results as $result) {
            $converter = new HtmlConverter(['strip_tags' => false]);
            $markdown = $converter->convert($result->$field);
            $result->$field = $markdown;
            $result->markdown = true;
            if ($this->save($result)) {
                echo "Converted $field #$result->id<br />";
            } else {
                echo "ERROR converting $field #$result->id<br />";
            }
        }
    }

    public function getAllIds()
    {
        return Cache::remember('allThoughtIds', function () {
            return $this->find('list')
                ->select(['id'])
                ->toArray();
        }, 'long');
    }

    public function generateFromUser($userId, $blockSize, $chainLength)
    {
        $ids = $this->find('list')
            ->select(['id'])
            ->where([
                'user_id' => $userId,
                'anonymous' => false
            ])
            ->order('rand()')
            ->toArray();
        $thoughts = $this->find('all')
            ->select(['thought'])
            ->where(function (QueryExpression $exp) use ($ids) {
                return $exp->in('id', $ids);
            })
            ->toArray();
        $thoughts = Hash::extract($thoughts, '{n}.thought');
        $sample = implode(' ', $thoughts);
        return $this->generate($sample, $blockSize, $chainLength);
    }

    public function generate($sample, $blockSize, $chainLength)
    {
        $chain = new EtherMarkovChain($sample, $blockSize);
        $chainLength = round($chainLength / $blockSize);
        $results = $chain->generate($chainLength);
        $results = $this->parseMarkdown($results);
        return strip_tags($results);
    }

    public function generateFromAll($limit, $blockSize, $chainLength)
    {
        $ids = $this->find('list')
            ->select(['id'])
            ->order('rand()')
            ->limit($limit)
            ->toArray();
        $thoughts = $this->find('all')
            ->select(['thought'])
            ->where(function (QueryExpression $exp) use ($ids) {
                return $exp->in('id', $ids);
            })
            ->toArray();
        $thoughts = Hash::extract($thoughts, '{n}.thought');
        $sample = implode(' ', $thoughts);
        return $this->generate($sample, $blockSize, $chainLength);
    }

    /**
     * Returns an array of thoughtword-candidates that appear in thoughts but
     * are not populated thoughtwords
     *
     * @return array
     */
    public function getUnpopulatedWords()
    {
        return Cache::remember('unpopulatedWords', function () {
            $thoughts = $this->find('all')
                ->select(['id', 'thought']);

            $allWords = [];
            foreach ($thoughts as $thought) {
                $words = preg_split("/\s+/", $thought->thought);
                foreach ($words as $word) {
                    $word = preg_replace('/[^a-zA-Z0-9]/', '', $word);
                    $word = strtolower($word);
                    if (isset($allWords[$word])) {
                        $allWords[$word]++;
                    } else {
                        $allWords[$word] = 1;
                    }
                }
            }

            $populatedThoughtwords = $this->getWords();
            foreach ($allWords as $word => $count) {
                if (in_array($word, $populatedThoughtwords)) {
                    unset($allWords[$word]);
                }
            }

            arsort($allWords);

            $allWords = array_keys($allWords);

            return array_filter($allWords, function ($word) {return $word != '';});
        }, 'daily');
    }

    /**
     * Returns an array of random unpopulated thoughtword-candidates from the
     * top $searchLimit most-used words
     *
     * @param int $count Size of array to return
     * @param int $searchLimit Word is taken from the top X possible words
     * @return array
     * @throws InternalErrorException
     */
    public function getSuggestedWords($count = 1, $searchLimit = 100)
    {
        if ($count > $searchLimit) {
            throw new InternalErrorException('Search limit cannot be less than count');
        }

        $unpopulatedWords = $this->getUnpopulatedWords();
        $topWords = array_slice($unpopulatedWords, 0, $searchLimit);
        shuffle($topWords);
        return array_slice($topWords, 0, $count);
    }

    /**
     * Selects thoughts with question marks
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findWithQuestions(Query $query, array $options)
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->like('thought', '%?%');
        });
    }

    /**
     * Returns an array of IDs for thoughts that have questions
     *
     * @return array
     */
    public function getIdsWithQuestions()
    {
        $results = $this->find('withQuestions')->select(['id'])->toArray();

        return Hash::extract($results, '{n}.id');
    }
}
