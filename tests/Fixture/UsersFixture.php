<?php

namespace SoftDelete\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\ORM\Table;

use SoftDelete\Model\Table\SoftDeleteTrait;

class UsersTable extends Table
{
    use SoftDeleteTrait;

    public function initialize(array $config)
    {
        $this->hasMany('Posts', [
            'dependent'        => true,
            'cascadeCallbacks' => true,
        ]);
    }
}

class UsersFixture extends TestFixture {

    public $fields = [
        'id'          => ['type' => 'integer'],
        'posts_count'  => ['type' => 'integer', 'default' => '0', 'null' => false],
        'deleted'     => ['type' => 'datetime', 'default' => null, 'null' => true],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']]
        ]
    ];
    public $records = [
        [
            'id'          => 1,
            'deleted'     => null,
            'posts_count' => 2
        ],
        [
            'id'          => 2,
            'deleted'     => null,
            'posts_count' => 0
        ],
    ];
}
