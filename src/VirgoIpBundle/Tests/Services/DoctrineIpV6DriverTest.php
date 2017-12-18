<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 4:12
 */

namespace VirgoIpBundle\Tests\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use VirgoIpBundle\Entity\IpV6Storage;

class DoctrineIpV6DriverTest extends KernelTestCase
{
    public const IP = '2001:db8:a0b:12f0::1';
    /** @var ContainerInterface */
    protected $container;

    public function setUp()
    {
        $_SERVER['KERNEL_DIR'] = '/var/www/virgoiptask/app/';
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        $ipStorage = $em->getRepository(IpV6Storage::class)->findOneBy(['ip' => self::IP]);
        if ($ipStorage) {
            $em->remove($ipStorage);
            $em->flush();
        }

    }

    public function testCreateNew()
    {
        $driver = $this->container->get('driver.ipv6.doctrine');
        $count = $driver->createNew(self::IP);

        $this->assertEquals(1, $count);
    }

    public function testGetCount()
    {
        $driver = $this->container->get('driver.ipv6.doctrine');
        $driver->createNew(self::IP);

        $count = $driver->getCount(self::IP);

        $this->assertEquals(1, $count);
    }

    public function testAdd()
    {
        $driver = $this->container->get('driver.ipv6.doctrine');
        $driver->createNew(self::IP);

        $count = $driver->add(self::IP);
        $this->assertEquals(2, $count);

        $count = $driver->add(self::IP);
        $this->assertEquals(3, $count);

        $count = $driver->getCount(self::IP);
        $this->assertEquals(3, $count);
    }
}