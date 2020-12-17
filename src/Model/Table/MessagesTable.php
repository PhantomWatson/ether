<?php
namespace App\Model\Table;

use App\Model\Entity\Message;
use Cake\Mailer\Email;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use League\HTMLToMarkdown\HtmlConverter;

/**
 * Messages Model
 *
 * @property UsersTable|\Cake\ORM\Association\BelongsTo $Recipients
 * @property UsersTable|\Cake\ORM\Association\BelongsTo $Senders
 * @method Message get($primaryKey, $options = [])
 * @method Message newEntity($data = null, array $options = [])
 * @method Message[] newEntities(array $data, array $options = [])
 * @method Message|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method Message patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method Message[] patchEntities($entities, array $data, array $options = [])
 * @method Message findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
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
        $this->setTable('messages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
                'rule' => function ($value) {
                    /** @var UsersTable $users */
                    $users = TableRegistry::getTableLocator()->get('Users');

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
     * @param int $userId The logged-in user's ID
     * @return array
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
                    /** @var Query $q */

                    return $q->select(['id', 'color']);
                },
                'Recipients' => function ($q) {
                    /** @var Query $q */

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

            /** @var Message $message */
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
                'verb' => ($message->sender_id == $userId) ? 'sent' : 'received',
                'unread' => $message->recipient_id == $userId && $message->received == 0
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
                    /** @var Query $q */

                    return $q->select(['id', 'color']);
                },
                'Recipients' => function ($q) {
                    /** @var Query $q */

                    return $q->select(['id', 'color']);
                }
            ])
            ->order(['Messages.created' => 'ASC']);
    }

    /**
     * Returns the count of unread messages received by this user
     *
     * @param $userId
     * @return int|null
     */
    public function getNewMessagesCount($userId)
    {
        return $this->find('all')
            ->select(['id'])
            ->where([
                'recipient_id' => $userId,
                'received' => 0,
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

    public function sendNotificationEmail($senderId, $recipientId, $message)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $recipient = $usersTable->get($recipientId);
        $sender = $usersTable->get($senderId);

        $email = new Email('new_message');
        $email->setTo($recipient->email);
        $email->setSubject('Ether: Message from #'.$sender->color);
        $email->setViewVars([
            'senderId' => $senderId,
            'senderColor' => $sender->color,
            'message' => $message,
            'loginUrl' => Router::url(['controller' => 'Users', 'action' => 'login'], true),
            'messageUrl' => Router::url(['controller' => 'Messages', 'action' => 'index', $sender->color], true),
            'accountUrl' => Router::url(['controller' => 'Users', 'action' => 'settings'], true),
            'siteUrl' => Router::url('/', true)
        ]);
        $email->setTemplate('new_message');
        $email->setLayout('default');
        $email->setEmailFormat('both');
        return $email->send();
    }
}
