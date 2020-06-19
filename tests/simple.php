<?php

require ('/home/denys/Desktop/hydrator/vendor/autoload.php');

use Aikrof\Hydrator\Entity\BaseEntity;
use Aikrof\Hydrator\Hydrator;
use Aikrof\Hydrator\Tests\TestEntity;

class Test
{
    use \Aikrof\Hydrator\Traits\HydratorTrait;

    public static function get()
    {
        return static::getHydrator();
    }
}

$entity = new TestEntity();

$i = new \Aikrof\Hydrator\Tests\InfEntity();
$info1 = new \Aikrof\Hydrator\Tests\Infoentity();
$info1->setInfo($i);
$info1->setText('asd');
$info2 = new \Aikrof\Hydrator\Tests\Infoentity();
$info2->setText('dsa');
$entity->setInfo($info1);


//$s = new \Aikrof\Hydrator\Tests\Extractor();
//var_dump(json_encode($s->extract($entity)));
//exit;

//var_dump(json_encode(Hydrator::extract($entity, true, ['data.info.asd','data.info.a', 'asd'])));

$asd = new TestEntity();
$asd->setFirstName('231');
$asd->setLastName('324');
$data = [
    'data' => [
        [
            'info' => [
                'b' => 'dsa',
                'a' => [$asd],
            ],
        ],
        [
            'info' => [
                'b' => 'asd',
                'a' => [$asd],
            ]
        ],
    ],
    'firstName' => ['asd']
];

$s = Hydrator::hydrate($data, TestEntity::class);

echo "<pre>";
var_dump($s);
exit;

exit;