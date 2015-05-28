<?php

namespace SoftDelete\Test\Fixture;

use Cake\ORM\Table;
use Cake\TestSuite\Fixture\TestFixture;

use SoftDelete\Model\Table\SoftDeleteTrait;

class TagsTable extends Table
{
    use SoftDeleteTrait;

    protected $softDeleteField = 'deleted_date';

    public function initialize(array $config)
    {
        $this->belongsToMany('Posts', [
            'through' => 'PostsTags',
            'joinTable' => 'posts_tags',
            'foreignKey' => 'tag_id',
            'targetForeignKey' => 'post_id'
        ]);
        $this->hasMany('PostsTags');
    }
}


class TagsFixture extends TestFixture {

    public $fields = [
        'id'          => ['type' => 'integer'],
        'name'     => ['type' => 'string'],
        'deleted_date'     => ['type' => 'datetime', 'default' => null, 'null' => true],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']]
        ]
    ];

    public $records = [
        [
            'id' => 1,
            'name' => 'Cat',
            'deleted_date' => null,
        ],
        [
            'id' => 2,
            'name' => 'Dog',
            'deleted_date' => null,
        ],
        [
            'id' => 3,
            'name' => 'Fish',
            'deleted_date' => '2015-04-15 09:46:00',
        ]
    ];
}


