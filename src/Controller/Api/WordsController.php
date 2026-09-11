<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Table\ThoughtsTable;
use Cake\Cache\Cache;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\TooManyRequestsException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\View\JsonView;

/**
 * Words API Controller
 */
class WordsController extends AppController
{
    /**
     * Cache config holding each word's counts (24 hour duration)
     */
    private const string RESULT_CACHE_CONFIG = 'wordSearch';

    /**
     * Cache config whose duration is the rate-limit window (5 seconds)
     */
    private const string RATE_LIMIT_CACHE_CONFIG = 'wordSearchRateLimit';

    /**
     * @return array
     */
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // An identity is still required (enforced in count()), but this
        // endpoint checks it itself so that an unauthenticated request gets a
        // 401 JSON response rather than a redirect to the HTML login page.
        $this->Authentication->disableIdentityCheck();
    }

    /**
     * Returns how many non-hidden thoughts, and how many comments on non-hidden
     * thoughts, contain a given word.
     *
     * Query string parameters:
     *  - word: the word to search for (required)
     *  - exclude_thought_id: a thought to leave out of both counts, along with
     *    its comments (optional; used when editing an existing thought so it
     *    doesn't count as usage of its own word)
     *
     * Results are cached for 24 hours, and each authenticated user may only
     * call this endpoint once every 5 seconds.
     *
     * @return void
     * @throws \Cake\Http\Exception\UnauthorizedException When the request is not authenticated
     * @throws \Cake\Http\Exception\BadRequestException When no usable word is provided
     * @throws \Cake\Http\Exception\TooManyRequestsException When called again within 5 seconds
     */
    public function count(): void
    {
        $identity = $this->Authentication->getIdentity();
        if ($identity === null) {
            throw new UnauthorizedException('You must be logged in to use this endpoint.');
        }

        $word = trim((string)$this->request->getQuery('word', ''));
        if ($word === '') {
            throw new BadRequestException('A "word" query string parameter is required.');
        }
        if (mb_strlen($word) > ThoughtsTable::MAX_THOUGHTWORD_LENGTH) {
            throw new BadRequestException('That word is too long.');
        }

        $excludeThoughtId = $this->request->getQuery('exclude_thought_id');
        if ($excludeThoughtId !== null && $excludeThoughtId !== '') {
            if (!ctype_digit((string)$excludeThoughtId)) {
                throw new BadRequestException('"exclude_thought_id" must be a positive integer.');
            }
            $excludeThoughtId = (int)$excludeThoughtId;
        } else {
            $excludeThoughtId = null;
        }

        $this->enforceRateLimit((string)$identity->getIdentifier());

        $counts = Cache::remember(
            'counts_' . md5(mb_strtolower($word) . '|' . ($excludeThoughtId ?? '')),
            function () use ($word, $excludeThoughtId) {
                $thoughts = TableRegistry::getTableLocator()->get('Thoughts');
                $comments = TableRegistry::getTableLocator()->get('Comments');

                return [
                    'comments' => $comments->countContainingWord($word, $excludeThoughtId),
                    'thoughts' => $thoughts->countContainingWord($word, $excludeThoughtId),
                ];
            },
            self::RESULT_CACHE_CONFIG
        );

        $this->set([
            'comments' => $counts['comments'],
            'thoughts' => $counts['thoughts'],
            'total' => $counts['comments'] + $counts['thoughts'],
            'word' => $word,
        ]);
        $this->viewBuilder()
            ->setOption('serialize', ['comments', 'thoughts', 'total', 'word'])
            ->setClassName('Json');
    }

    /**
     * Records that the given user has just called this endpoint, throwing if
     * they already called it within the rate-limit window.
     *
     * @param string $userId Identifier of the authenticated user
     * @return void
     * @throws \Cake\Http\Exception\TooManyRequestsException
     */
    private function enforceRateLimit(string $userId): void
    {
        $key = 'user_' . $userId;
        if (Cache::read($key, self::RATE_LIMIT_CACHE_CONFIG) !== null) {
            throw new TooManyRequestsException('Please wait at least 5 seconds between requests.');
        }
        Cache::write($key, true, self::RATE_LIMIT_CACHE_CONFIG);
    }
}
