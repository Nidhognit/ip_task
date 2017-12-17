<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 2:26
 */

namespace VirgoIpBundle\Services\Drivers\BTreeDrivers;

use VirgoIpBundle\Services\Drivers\DriverInterface;

class BTreeIpV4Driver implements DriverInterface
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