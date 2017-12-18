<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 3:33
 */

namespace VirgoIpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * By default, doctrine can't create BTree index
 * you can use next SQl:
 * CREATE TABLE IpV6Storage (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(39) NOT NULL,
 * count INT NOT NULL, UNIQUE INDEX UNIQ_748A8CCEA5E3B32D (ip), INDEX ip_v6 (ip) USING BTREE, PRIMARY KEY(id))
 * DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
 *
 * @ORM\Table(name="IpV6Storage", indexes={@ORM\Index(name="ip_v6", columns={"ip"})})
 * @ORM\Entity()
 */
class IpV6Storage
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $ip
     *
     * @ORM\Column(name="ip", type="string", length=39, nullable=false, unique=true)
     */
    protected $ip = 1;

    /**
     * @var int $count
     *
     * @ORM\Column(name="count", type="integer", nullable=false, unique=false)
     */
    protected $count = 1;

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}