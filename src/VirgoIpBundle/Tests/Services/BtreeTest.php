<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 5:24
 */

namespace VirgoIpBundle\Tests\Services;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use VirgoIpBundle\Services\Drivers\BTreeDrivers\Btree;

class BtreeTest extends KernelTestCase
{
    public function testGet()
    {
        $data = [
            '127.0.0.1' => [
                'child' => [
                    '126.10.0.0' => [
                        'count' => 5,
                    ],
                    '126.09.0.0' => [
                        'count' => 3,
                    ],
                ],
                'count' => 2,
            ],
            '168.0.0.1' => [
                'child' => [
                    '167.2.1.4' => [
                        'count' => 4,
                    ],
                ],
                'count' => 150,
            ],
        ];
        $btree = new Btree();
        $btree->setBtreeData($data);

        $count = $btree->find('127.0.0.1');
        $this->assertEquals(2, $count);

        $count = $btree->find('168.0.0.1');
        $this->assertEquals(150, $count);

        $count = $btree->find('126.10.0.0');
        $this->assertEquals(5, $count);

        $count = $btree->find('126.09.0.0');
        $this->assertEquals(3, $count);

        $count = $btree->find('167.2.1.4');
        $this->assertEquals(4, $count);

        $count = $btree->find('167.2.1.5');
        $this->assertEquals(0, $count);
    }

    public function testSet()
    {
        $btree = new Btree();

        $count = $btree->add('127.0.0.1');
        $this->assertEquals(1, $count);

        $count = $btree->add('127.0.0.1');
        $this->assertEquals(2, $count);

        $count = $btree->add('127.0.0.1');
        $this->assertEquals(3, $count);

        $count = $btree->add('127.0.0.0');
        $this->assertEquals(1, $count);

        $count = $btree->add('127.0.0.0');
        $this->assertEquals(2, $count);

        $count = $btree->add('127.0.0.0');
        $this->assertEquals(3, $count);

        $btree->add('126.0.0.13');
        $btree->add('126.0.0.10');
        $btree->add('126.0.0.15');
        $btree->add('126.0.0.11');
        $btree->add('126.0.0.12');

        $count = $btree->add('126.0.0.12');
        $this->assertEquals(2, $count);

        $btree->add('186.0.0.12');
        $btree->add('166.0.0.12');
        $count = $btree->add('166.0.0.12');
        $this->assertEquals(2, $count);
    }
}