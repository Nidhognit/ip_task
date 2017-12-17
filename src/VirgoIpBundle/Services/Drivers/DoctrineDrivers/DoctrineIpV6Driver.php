<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 2:23
 */

namespace VirgoIpBundle\Services\Drivers\DoctrineDrivers;

use Doctrine\ORM\EntityManager;
use VirgoIpBundle\Entity\IpV6Storage;
use VirgoIpBundle\Services\Drivers\DriverInterface;

class DoctrineIpV6Driver implements DriverInterface
{
    /** @var EntityManager */
    protected $em;

    /**
     * DoctrineIpV6Driver constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $ip
     * @return int
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(string $ip): int
    {
        $count = $this->getCount($ip);
        if ($count > 0) {
            $count++;
            $this->update($ip);
        } else {
            $count = $this->createNew($ip);
        }

        return $count;
    }

    public function update(string $ip): void
    {
        $conn = $this->em->getConnection();
        $query = 'UPDATE IpV6Storage v6 SET v6.count = v6.count + 1 WHERE v6.ip = :ip';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $stmt->execute();
        $this->em->clear(IpV6Storage::class);
    }

    /**
     * @param string $ip
     * @return int
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNew(string $ip): int
    {
        try {
            $ipStorage = new IpV6Storage();
            $ipStorage->setIp($ip);
            $this->em->persist($ipStorage);
            $this->em->flush();
            $count = $ipStorage->getCount();
        } catch (\Throwable $exception) {
            $count = $this->getCount($ip);
            $this->add($ip);
            $count++;
        }

        return $count;
    }

    /**
     * @param string $ip
     * @return int
     */
    public function getCount(string $ip): int
    {
        $ipStorage = $this->em->getRepository(IpV6Storage::class)->findOneBy(['ip' => $ip]);
        if ($ipStorage) {
            return $ipStorage->getCount();
        }

        return 0;
    }
}