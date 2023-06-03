<?php
namespace App\Model\Table;

use App\Application;
use App\Model\Entity\Message;
use Cake\Mailer\Mailer;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\Validation\Validator;

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
    public function initialize(array $config): void
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
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->scalar('id')
            ->numeric('id');

        $validator
            ->scalar('recipient_id')
            ->numeric('recipient_id')
            ->add('recipient_id', 'acceptsMessages', [
                'rule' => function ($value) {
                    /** @var \App\Model\Table\UsersTable $usersTable */
                    $usersTable = TableRegistry::getTableLocator()->get('Users');

                    return $usersTable->acceptsMessages($value);
                },
                'message' => 'Sorry, this Thinker has chosen not to receive messages. Your message was not sent. :('
            ])
            ->requirePresence('recipient_id', 'create');

        $validator
            ->scalar('sender_id')
            ->numeric('sender_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('sender_id', 'create');

        $validator
            ->scalar('message')
            ->requirePresence('message', 'create')
            ->allowEmptyString(
                'message',
                'You can\'t send a blank message. It would be terribly disappointing for the recipient.',
                false
            );

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker
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
        /** @var Message[] $newestMessages */
        $newestMessages = $query
            ->select([
                'Messages.sender_id',
                'Messages.recipient_id',
                'created' => $query->func()->max('Messages.created')
            ])
            ->distinct(['Messages.sender_id', 'Messages.recipient_id'])
            ->contain([
                'Senders' => function (Query $q) {
                    return $q->select(['id', 'color']);
                },
                'Recipients' => function (Query $q) {
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
            ->all();

        $conversations = [];
        foreach ($newestMessages as $newestMessage) {
            $otherUser = ($newestMessage->sender_id == $userId) ? 'recipient' : 'sender';
            $otherUserId = $newestMessage->$otherUser->id;

            // Ignore all but the newest message between these users
            if (isset($conversations[$otherUserId]) && $newestMessage['created'] < $conversations[$otherUserId]['time']) {
                continue;
            }

            /** @var Message $messageDetails */
            $messageDetails = $this->find('all')
                ->select(['message', 'received', 'sender_id', 'recipient_id'])
                ->where([
                    'sender_id' => $newestMessage['sender']['id'],
                    'recipient_id' => $newestMessage['recipient']['id'],
                    'created' => $newestMessage['created'],
                ])
                ->order(['created' => 'DESC'])
                ->first();

            $conversations[$otherUserId] = [
                'color' => $newestMessage->$otherUser->color,
                'time' => $newestMessage->created,
                'message' => Text::truncate($messageDetails->message, 100, ['exact' => false]),
                'verb' => $messageDetails->sender_id == $userId ? 'sent' : 'received',
                'unread' => $messageDetails->recipient_id == $userId && $messageDetails->received == 0,
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

    /**
     * Marks messages received by the logged-in user and sent by the specified penpal as being read
     *
     * @param int $userId The logged-in user's ID
     * @param int $penpalId The other user's ID
     */
    public function setConversationAsRead(int $userId, int $penpalId)
    {
        $messages = $this->find('all')
            ->select(['id'])
            ->where([
                'received' => 0,
                'sender_id' => $penpalId,
                'recipient_id' => $userId,
            ]);

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
     * @param int $senderId
     * @param int $recipientId
     * @param string $message
     * @return void
     */
    public function sendNotificationEmail($senderId, $recipientId, $message): array
    {
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $recipient = $usersTable->get($recipientId);
        $sender = $usersTable->get($senderId);

        $mailer = new Mailer();
        $mailer
            ->setEmailFormat(\Cake\Mailer\Message::MESSAGE_BOTH)
            ->setTo($recipient->email)
            ->setFrom(Application::EMAIL_FROM)
            ->setSender(Application::EMAIL_FROM)
            ->setSubject('Ether: Message from #'.$sender->color)
            ->viewBuilder()
                ->setLayout('default')
                ->setTemplate('new_message')
                ->setVars([
                    'senderId' => $senderId,
                    'senderColor' => $sender->color,
                    'message' => $message,
                    'loginUrl' => Router::url(['controller' => 'Users', 'action' => 'login'], true),
                    'messageUrl' => Router::url(['controller' => 'Messages', 'action' => 'index', $sender->color], true),
                    'accountUrl' => Router::url(['controller' => 'Users', 'action' => 'settings'], true),
                    'siteUrl' => Router::url('/', true)
                ]);
        $mailer->deliver();
    }
}
