<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 2:23
 */

namespace VirgoIpBundle\Services\Drivers\DoctrineDrivers;

use VirgoIpBundle\Services\Drivers\DriverInterface;

class DoctrineIpV6Driver implements DriverInterface
{
    public function add(string $ip): int
    {

        return 1;
    }

    public function createNew(string $ip): int
    {
        return 1;

    }

    public function getCount(string $ip): int
    {
        return 1;

    }
}