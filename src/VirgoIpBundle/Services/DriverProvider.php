<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 2:03
 */

namespace VirgoIpBundle\Services;

use VirgoIpBundle\Exceptions\UnknownDriverException;
use VirgoIpBundle\Services\Drivers\DriverInterface;

class DriverProvider
{
    /** @var DriverInterface */
    protected $ipv4driver;

    /** @var DriverInterface */
    protected $ipv6driver;

    /**
     * @param string $ip
     * @return int
     * @throws UnknownDriverException
     */
    public function addIp(string $ip): int
    {
        $driver = $this->getIpDriver($ip);
        $count = $driver->getCount($ip);
        if ($count > 0) {
            $count++;
            $driver->add($ip);
        } else {
            $count = $driver->createNew($ip);
        }

        return $count;
    }

    /**
     * @param string $ip
     * @return int
     * @throws UnknownDriverException
     */
    public function getIpCount(string $ip): int
    {
        $driver = $this->getIpDriver($ip);

        if ($driver->getCount($ip) > 0) {
            return $driver->add($ip);
        }

        return 0;
    }

    /**
     * @param string $ip
     * @return DriverInterface
     * @throws UnknownDriverException
     */
    protected function getIpDriver(string $ip): DriverInterface
    {
        switch (true) {
            case filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4):
                return $this->ipv4driver;
                break;
            case filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6):
                return $this->ipv6driver;
                break;
        }

        throw new UnknownDriverException();
    }

    /**
     * @param DriverInterface $ipv4driver
     */
    public function setIpv4driver(DriverInterface $ipv4driver): void
    {
        $this->ipv4driver = $ipv4driver;
    }

    /**
     * @param DriverInterface $ipv6driver
     */
    public function setIpv6driver(DriverInterface $ipv6driver): void
    {
        $this->ipv6driver = $ipv6driver;
    }
}