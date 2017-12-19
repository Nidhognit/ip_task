<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 2:03
 */

namespace VirgoIpBundle\Services;

use VirgoIpBundle\Exceptions\InvalidIpException;
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

        return $driver->add($ip);
    }

    /**
     * @param string $ip
     * @return int
     * @throws UnknownDriverException
     */
    public function getIpCount(string $ip): int
    {
        $driver = $this->getIpDriver($ip);

        return $driver->getCount($ip);
    }

    /**
     * @param null|string $ip
     * @return string
     * @throws InvalidIpException
     */
    public function validateIp(?string $ip): string
    {
        if ($ip === null || !filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidIpException();
        }

        return $ip;
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