<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 2:04
 */

namespace VirgoIpBundle\Services\Drivers;

interface DriverInterface
{
    /**
     * @param string $ip
     * @return int
     */
    public function add(string $ip): int;

    /**
     * @param string $ip
     * @return int
     */
    public function getCount(string $ip): int;
}