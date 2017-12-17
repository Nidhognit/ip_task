<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 7:14
 */

namespace VirgoIpBundle\Services\Drivers\BTreeDrivers;


interface BtreeInterface
{
    /**
     * @param string $ip
     * @return int|null
     */
    public function find(string $ip): int;

    /**
     * @param string $ip
     * @return int
     */
    public function add(string $ip): int;
}