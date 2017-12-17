<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 2:26
 */

namespace VirgoIpBundle\Services\Drivers\BTreeDrivers;

use Doctrine\ORM\EntityManager;
use VirgoIpBundle\Entity\IpV4Storage;
use VirgoIpBundle\Services\Drivers\DriverInterface;

class BTreeIpV4Driver implements DriverInterface
{
    /** @var BtreeInterface */
    protected $btree;

    /** @var EntityManager */
    protected $em;

    /** @var IpV4Storage */
    protected $ipStorage;

    /**
     * BTreeIpV4Driver constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function getBtree()
    {
        if (!$this->btree) {
            $this->btree = new MyBtree();
            $this->ipStorage = $this->em->getRepository(IpV4Storage::class)->find(1);
            if (!$this->ipStorage) {
                $this->ipStorage = new IpV4Storage();
            } else {
                $this->btree->setBtreeData(json_decode($this->ipStorage->getData(), true));
            }
        }

        return $this->btree;
    }

    public function add(string $ip): int
    {
        $count = $this->getBtree()->add($ip);
        $this->ipStorage->setData(json_encode($this->getBtree()->getBtreeData()));
        $this->em->persist($this->ipStorage);
        $this->em->flush();

        return $count;
    }

    public function getCount(string $ip): int
    {
        return $this->getBtree()->find($ip);
    }
}