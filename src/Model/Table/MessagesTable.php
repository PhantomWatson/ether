<?php
namespace App\Model\Table;

use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use League\HTMLToMarkdown\HtmlConverter;

/**
 * Messages Model
 */
class MessagesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('messages');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Recipients', [
            'className' => 'Users',
            'foreignKey' => 'recipient_id'
        ]);
        $this->belongsTo('Senders', [
            'className' => 'Users',
            'foreignKey' => 'sender_id'
        ]);
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
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->add('recipient_id', 'valid', ['rule' => 'numeric'])
            ->add('recipient_id', 'acceptsMessages', [
                'rule' => function ($value, $context) {
                    $users = TableRegistry::get('Users');
                    return $users->acceptsMessages($value);
                },
                'message' => 'Sorry, this Thinker has chosen not to receive messages. Your message was not sent. :('
            ])
            ->requirePresence('recipient_id', 'create')
            ->notEmpty('recipient_id')
            ->add('sender_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('sender_id', 'create')
            ->notEmpty('sender_id')
            ->requirePresence('message', 'create')
            ->notEmpty('message', 'You can\'t send a blank message. It would be terribly disappointing for the recipient.');

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
        $rules->add($rules->existsIn(['recipient_id'], 'Recipients'));
        $rules->add($rules->existsIn(['sender_id'], 'Senders'));
        return $rules;
    }

    public function userHasNewMessages($userId)
    {
        $count = $this->find('all')
            ->where([
                'recipient_id' => $userId,
                'received' => 0
            ])
            ->count();
        return $count > 0;
    }

    /**
     * Returns an array of metadata about conversations the user has engaged in
     *
     * @param int $userId
     */
    public function getConversationsIndex($userId)
    {
        $query = $this->find('all');
        $results = $query
            ->select([
                'Messages.sender_id',
                'Messages.recipient_id',
                'created' => $query->func()->max('Messages.created')
            ])
            ->distinct(['Messages.sender_id', 'Messages.recipient_id'])
            ->contain([
                'Senders' => function ($q) {
                    return $q->select(['id', 'color']);
                },
                'Recipients' => function ($q) {
                    return $q->select(['id', 'color']);
                }
            ])
            ->where([
                'OR' => [
                    'Messages.sender_id' => $userId,
                    'Messages.recipient_id' => $userId
                ]
            ])
            ->order(['created' => 'DESC'])
            ->toArray();

        $conversations = [];
        foreach ($results as $result) {
            $otherUser = ($result['sender']['id'] == $userId) ? 'recipient' : 'sender';
            $otherUserId = $result[$otherUser]['id'];

            if (isset($conversations[$otherUserId]) && $result['created'] < $conversations[$otherUserId]['time']) {
                continue;
            }

            $message = $this->find('all')
                ->select(['message', 'received'])
                ->where([
                    'sender_id' => $result['sender']['id'],
                    'recipient_id' => $result['recipient']['id'],
                    'created' => $result['created'],
                ])
                ->order(['created' => 'DESC'])
                ->first();

            $conversations[$otherUserId] = [
                'color' => $result[$otherUser]['color'],
                'time' => $result['created'],
                'message' => Text::truncate($message->message, 100, ['exact' => false]),
                'verb' => ($result['sender']['id'] == $userId) ? 'sent' : 'received',
                'unread' => $message->received == 0
            ];
        }
        return $conversations;
    }

    /**
     * Returns a group of messages exchanged between two users
     * @param int $userId
     * @param int $penpalId
     * @return Query
     */
    public function getConversation($userId, $penpalId)
    {
        return $this->find('all')
            ->where([
                'OR' => [
                    [
                        'sender_id' => $userId,
                        'recipient_id' => $penpalId
                    ],
                    [
                        'sender_id' => $penpalId,
                        'recipient_id' => $userId
                    ]
                ]
            ])
            ->select([
                'id',
                'sender_id',
                'recipient_id',
                'received',
                'created',
                'message'
            ])
            ->contain([
                'Senders' => function ($q) {
                    return $q->select(['id', 'color']);
                },
                'Recipients' => function ($q) {
                    return $q->select(['id', 'color']);
                }
            ])
            ->order(['Messages.created' => 'ASC']);
    }

    public function getNewMessagesCount($userId)
    {
        return $this->find('all')
            ->select(['id'])
            ->where([
                'OR' => [
                    'sender_id' => $userId,
                    'recipient_id' => $userId
                ],
                'received' => 0
            ])
            ->count();
    }

    public function setConversationAsRead($userId, $penpalId)
    {
        $messages = $this->find('all')
            ->select(['id'])
            ->where([
                'received' => 0,
                'OR' => [
                    [
                        'sender_id' => $userId,
                        'recipient_id' => $penpalId
                    ],
                    [
                        'sender_id' => $penpalId,
                        'recipient_id' => $userId
                    ]
                ]
            ]);
        if (empty($messages)) {
            return;
        }
        foreach ($messages as $message) {
            $message->received = 1;
            $this->save($message);
        }
    }

    /**
     * Returns the number of messages exchanged between two users
     *
     * @param int $userId
     * @param int $penpalId
     * @return int
     */
    public function getConversationCount($userId, $penpalId)
    {
        return $this->find('all')
            ->where([
                'OR' => [
                    [
                        'sender_id' => $userId,
                        'recipient_id' => $penpalId
                    ],
                    [
                        'sender_id' => $penpalId,
                        'recipient_id' => $userId
                    ]
                ]
            ])
            ->count();
    }

    /**
     * Removs slashes that were a leftover of the anti-injection-attack strategy of the olllllld Ether
     */
    public function overhaulStripSlashes()
    {
        $messages = $this->find('all')
            ->select(['id', 'message'])
            ->where(['message LIKE' => '%\\\\%'])
            ->order(['id' => 'ASC']);
        foreach ($messages as $message) {
            echo $message->message;
            $fixed = stripslashes($message->message);
            $message->message = $fixed;
            $this->save($message);
            echo " => $fixed<br />";
        }
    }

    public function overhaulToMarkdown()
    {
        $field = 'message';
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
}
