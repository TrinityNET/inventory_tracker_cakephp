<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Product Entity
 *
 * @property int $id
 * @property string $name
 * @property int $quantity
 * @property string $price
 * @property string|null $status
 * @property \Cake\I18n\DateTime $last_updated
 * @property bool $deleted
 */
class Product extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'quantity' => true,
        'price' => true,
        'status' => true,
        'last_updated' => true,
        'deleted' => true,
    ];
}
