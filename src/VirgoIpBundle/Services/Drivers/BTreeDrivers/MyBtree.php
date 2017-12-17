<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 17.12.17
 * Time: 5:09
 */

namespace VirgoIpBundle\Services\Drivers\BTreeDrivers;


class MyBtree
{

    protected const SECOND_LENGTH = 5;
    /**
     * @var array
     */
    protected $btreeData = [];

    /**
     * @return array
     */
    public function getBtreeData(): array
    {
        return $this->btreeData;
    }

    /**
     * @param array $btreeData
     */
    public function setBtreeData(array $btreeData): void
    {
        $this->btreeData = $btreeData;
    }

    public function root()
    {

    }

    public function find(string $ip): ?int
    {
        foreach ($this->btreeData as $key => $valueData) {
            if ($ip === $key) {

                return $valueData['count'];
            } elseif (strcasecmp($ip, $key) < 0) {
                if (isset($valueData['child'][$ip])) {

                    return $valueData['child'][$ip]['count'];
                }
            }
        }

        return null;
    }

    public function add(string $ip): int
    {
        if (empty($this->btreeData)) {
            return $this->boot($ip);
        }
        foreach ($this->btreeData as $key => &$valueData) {
            if ($ip === $key) {
                $valueData['count'] += 1;

                return $valueData['count'];
            } elseif (strcasecmp($ip, $key) < 0) {
                foreach ($valueData['child'] as $secondKey => &$secondData) {
                    if ($ip === $secondKey) {
                        $secondData['count'] += 1;

                        return $secondData['count'];
                    }
                }
                $this->addChild($valueData, $ip);
                if (count($valueData['child']) > self::SECOND_LENGTH) {
                    $this->splitSheet($valueData, $key);
                }

                return 1;
            }
        }
        $this->btreeData[$ip] = [
            'count' => 1,
            'child' => [],
        ];

        return 1;
    }

    protected function splitSheet(array &$sheetData, $parentIp)
    {
        $leftChildList = [];
        $rightChildList = [];
        $newParent = [];
        $newParentIp = '';
        $i = 0;
        ksort($sheetData['child']);
        foreach ($sheetData['child'] as $ipKey => $valueData) {
            if ($i < 2) {
                $leftChildList[$ipKey] = $valueData;
            } elseif ($i === 2) {
                $newParentIp = $ipKey;
                $newParent = $valueData;
            } else {
                $rightChildList[$ipKey] = $valueData;
            }
            $i++;
        }
        $newParent['child'] = $leftChildList;
        $this->btreeData[$newParentIp] = $newParent;
        $this->btreeData[$parentIp]['child'] = $rightChildList;
        ksort($this->btreeData);
    }

    protected function addChild(array &$sheetData, string $ip): void
    {
        $sheetData['child'][$ip] = [
            'count' => 1,
        ];

    }

    protected function boot(string $ip)
    {
        $this->btreeData = [
            $ip => [
                'count' => 1,
                'child' => [],
            ],
        ];

        return 1;
    }
}