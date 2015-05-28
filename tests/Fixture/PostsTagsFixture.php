<?php

namespace SoftDelete\Test\Fixture;

use Cake\ORM\Table;
use Cake\TestSuite\Fixture\TestFixture;

use SoftDelete\Model\Table\SoftDeleteTrait;

class PostsTagsTable extends Table
{
    use SoftDeleteTrait;

    public function initialize(array $config)
    {
        $this->belongsTo('Tags');
        $this->belongsTo('Posts');
    }
}


class PostsTagsFixture extends TestFixture {

    public $fields = [
        'id' => ['type' => 'integer'],
        'post_id' => ['type' => 'integer'],
        'tag_id' => ['type' => 'integer'],
        'deleted'     => ['type' => 'datetime', 'default' => null, 'null' => true],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']]
        ]
    ];

    public $records = [
        [
            'id' => 1,
            'post_id' => 1,
            'tag_id' => 1,
            'deleted' => null,
        ],
        [
            'id' => 2,
            'post_id' => 1,
            'tag_id' => 2,
            'deleted' => '2015-05-18 15:04:00',
        ],
    ];
}


