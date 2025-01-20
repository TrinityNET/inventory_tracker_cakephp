<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Products Model
 *
 * @method \App\Model\Entity\Product newEmptyEntity()
 * @method \App\Model\Entity\Product newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Product> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Product get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Product findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Product patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Product> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Product|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Product saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Product>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Product>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Product>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Product> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Product>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Product>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Product>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Product> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ProductsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('products');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => ['last_updated' => 'always'] // Function to update last_updated on every save.
                // Although database will do this automatically with SQLite.
            ]
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
        ->scalar('name')
        ->maxLength('name', 50, 'Name cannot be more than 50 characters')
        ->minLength('name', 3, 'Name must be greater than 3 characters')
        ->requirePresence('name', 'create')
        ->notEmptyString('name', 'Name is required')
        ->add('name', 'unique', [
            'rule' => 'validateUnique',
            'provider' => 'table',
            'message' => 'The name must be unique.'
        ])
        ->add('name', 'promoPriceRule', [
            'rule' => function ($value, $context) {
                if (strpos(strtolower($value), 'promo') !== false && $context['data']['price'] >= 50) {
                    return false;
                }
                return true;
            },
            'message' => 'Products with "promo" in the name must have a price less than 50.',
        ]);

        $validator
            ->integer('quantity')
            ->notEmptyString('quantity')
            ->greaterThanOrEqual('quantity', 0, 'Quantity must be at least 0.')
            ->lessThanOrEqual('quantity', 1000, 'Quantity must not exceed 1000.')
            ->add('name', 'priceQuantityRule', [
                'rule' => function ($value, $context) {
                    if ($context['data']['price'] > 100 && $value < 10) {
                        return false;
                    }
                    return true;
                },
                'message' => 'Products with a price of greater than 100 must have a minimum quantity of 10.',
            ]);

        $validator
            ->decimal('price')
            ->greaterThan('price', 0, 'Price must be greater than 0.')
            ->lessThanOrEqual('price', 10000, 'Price must not exceed 10,000.')
            ->requirePresence('price', 'create')
            ->notEmptyString('price');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->allowEmptyString('status');

        $validator
            ->dateTime('last_updated')
            ->notEmptyDateTime('last_updated');

        $validator
            ->boolean('deleted')
            ->notEmptyString('deleted');

        return $validator;
    }

    public function beforeSave($event, $entity, $options) // Dynamically calculate 'status' column based on quantity.
    {
        if ($entity->isDirty('quantity')) { // If quantity has been changed
            if ($entity->quantity > 10) {
                $entity->status = 'in stock';
            } elseif ($entity->quantity >= 1 && $entity->quantity <= 10) {
                $entity->status = 'low stock';
            } else {
                $entity->status = 'out of stock';
            }
        }
    }
}
