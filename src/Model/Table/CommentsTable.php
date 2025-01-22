<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Comments Model
 *
 * @property \App\Model\Table\ThoughtsTable|\Cake\ORM\Association\BelongsTo $Thoughts
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @method \App\Model\Entity\Comment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Comment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Comment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Comment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Comment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Comment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Comment findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommentsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable('comments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Thoughts', [
            'foreignKey' => 'thought_id'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
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
            ->scalar('thought_id')
            ->numeric('thought_id')
            ->requirePresence('thought_id', 'create')
            ->add('thought_id', 'comments_enabled', [
                'rule' => function ($value, $context) {
                    $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
                    /** @var \App\Model\Entity\Thought $thought Thought record */
                    $thought = $thoughtsTable->find()
                        ->select([
                            'comments_enabled',
                            'hidden',
                        ])
                        ->where(['id' => $value])
                        ->first();

                    return $thought && $thought->comments_enabled && !$thought->hidden;
                },
                'message' => 'That thought has comments disabled'
            ]);

        $validator
            ->scalar('user_id')
            ->numeric('user_id')
            ->requirePresence('user_id', 'create');

        $validator
            ->scalar('comment')
            ->requirePresence('comment', 'create')
            ->allowEmptyString(
                'comment',
                'Please don\'t leave an empty comment. It would be terrible confusing to everyone.',
                false
            );

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
    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker
    {
        $rules->add($rules->existsIn(['thought_id'], 'Thoughts'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }
}
