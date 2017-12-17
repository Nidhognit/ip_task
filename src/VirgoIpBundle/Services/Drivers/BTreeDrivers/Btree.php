<?php

namespace VirgoIpBundle\Services\Drivers\BTreeDrivers;

class Btree
{
    public const SIZEOF_HEADER = 6;

    public const HEADER = "\xffbtree";

    public const NODE_SLOTS = 16;

    public const SIZEOF_INT = 4;

    public const SIZEOF_NODE_TYPE = 2;

    public const NODE_TYPE_VALUE = 'kv';

    public const NODE_TYPE_POINTER = 'kp';

    public const NODE_CACHE_SIZE = 64;


    /**
     * @var resource
     */
    private $resource;

    /**
     * @var array
     */
    private $nodeCache = [];

    /**
     * @param $resource
     * @param $fileName
     */
    private function __construct($resource, $fileName)
    {
        $this->resource = $resource;
    }

    /**
     * free resource.
     */
    public function __destruct()
    {
        fclose($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        $lookup = $this->lookup($key);
        $leaf = end($lookup);
        if ($leaf !== null && isset($leaf[$key])) {
            return $leaf[$key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value)
    {
        if (!flock($this->resource, LOCK_EX)) {
            return false;
        }
        if (fseek($this->resource, 0, SEEK_END) === -1) {
            flock($this->resource, LOCK_UN);

            return false;
        }
        if (($pos = ftell($this->resource)) === false) {
            flock($this->resource, LOCK_UN);

            return false;
        }
        $cursor = $pos;

        $lookup = $this->lookup($key);
        $node = array_pop($lookup);
        if ($node === null) {
            return false;
        }

        $index = current(array_keys($node));
        $nodeType = self::NODE_TYPE_VALUE;
        $newIndex = null;
        if ($value === null) {
            unset($node[$key]);
        } else {
            $node[$key] = $value;
        }

        do {
            if (count($node) <= (int)(self::NODE_SLOTS / 2) && !empty($lookup)) {
                $upNode = (array) array_pop($lookup);
                $newIndex = current(array_keys($upNode));
                $sibling = $prev = [null, null];

                foreach ($upNode as $k => $v) {
                    if ($index === $k) {
                        $sibling = $prev;
                    } elseif ($index === $prev[0]) {
                        $sibling = [$k, $v];
                    }

                    if ($sibling[0] !== null) {
                        [$siblingType, $siblingNode] = $this->node($sibling[1]);
                        if ($siblingType === null || $siblingNode === null) {
                            ftruncate($this->resource, $pos);
                            flock($this->resource, LOCK_UN);

                            return false;
                        }
                        $node = array_merge($node, $siblingNode);
                        unset($upNode[$sibling[0]]);
                    }

                    $prev = [$k, $v];
                    $sibling = [null, null];
                }

                $lookup[] = $upNode;
            }

            ksort($node, SORT_STRING);
            if (count($node) <= self::NODE_SLOTS) {
                $nodes = [$node];
            } else {
                $nodes = array_chunk($node, ceil(count($node) / ceil(count($node) / self::NODE_SLOTS)), true);
            }

            $upNode = array_merge([], (array) array_pop($lookup));
            if ($newIndex === null) {
                $newIndex = current(array_keys($upNode));
            }
            unset($upNode[$index]);

            foreach ($nodes as $nodeItem) {
                $serialized = self::serializeNode($nodeType, $nodeItem);
                $toWrite = pack('N', strlen($serialized)).$serialized;
                if (fwrite($this->resource, $toWrite, strlen($toWrite)) !== strlen($toWrite)) {
                    ftruncate($this->resource, $pos);
                    flock($this->resource, LOCK_UN);

                    return false;
                }
                $upNode[current(array_keys($nodeItem))] = $cursor;
                $cursor += strlen($toWrite);
            }

            $nodeType = self::NODE_TYPE_POINTER;
            $index = $newIndex;
            $newIndex = null;

            if (count($upNode) <= 1) {
                $root = current(array_values($upNode));
                break;
            } else {
                $lookup[] = $upNode;
            }
        } while ($node = array_pop($lookup));

        if (!(fflush($this->resource) &&
            self::header($this->resource, $root) &&
            fflush($this->resource))) {
            ftruncate($this->resource, $pos);
            flock($this->resource, LOCK_UN);

            return false;
        }

        flock($this->resource, LOCK_UN);

        return true;
    }

    /**
     * Look up key.
     *
     * @param string
     * @param string
     * @param array
     *
     * @return array
     */
    private function lookup($key)
    {
        [$nodeType, $node] = $this->root();
        if ($nodeType === null || $node === null) {
            return [null, null];
        }

        $result = [];
        do {
            $result[] = $node;
            if ($nodeType === self::NODE_TYPE_VALUE) {
                $node = null;
            } else {
                $keys = array_keys($node);
                $l = 0;
                $r = count($keys);

                while ($l < $r) {
                    $i = $l + (int)(($r - $l) / 2);
                    if (strcmp($keys[$i], $key) < 0) {
                        $l = $i + 1;
                    } else {
                        $r = $i;
                    }
                }

                $l = max(0, $l + ($l >= count($keys) ? -1 : (strcmp($keys[$l], $key) <= 0 ? 0 : -1)));

                [$nodeType, $node] = $this->node($node[$keys[$l]]);
                if ($nodeType === null || $node === null) {
                    return [null, null];
                }
            }
        } while ($node !== null);

        return $result;
    }

    /**
     * @return array
     */
    private function root()
    {
        if (null === ($pointer = $this->findRoot())) {
            return [null, null];
        }

        return $this->node($pointer);
    }

    /**
     * @return int
     */
    private function findRoot()
    {
        if (fseek($this->resource, -(self::SIZEOF_HEADER + self::SIZEOF_INT), SEEK_END) === -1) {
            return null;
        }

        if (strlen($data = fread($this->resource, self::SIZEOF_INT + self::SIZEOF_HEADER))
            !== self::SIZEOF_INT + self::SIZEOF_HEADER) {
            return null;
        }

        $root = substr($data, 0, self::SIZEOF_INT);

        if (substr($data, self::SIZEOF_INT) !== self::HEADER) {
            $root = null;

            if (($size = ftell($this->resource)) === false) {
                return null;
            }
            for ($i = -1; ($off = $i * 128) + $size > 128; --$i) {
                if (fseek($this->resource, $off, SEEK_END) === -1) {
                    return null;
                }
                $data = fread($this->resource, 256);
                if (($pos = strrpos($data, self::HEADER)) !== false) {
                    if ($pos === 0) {
                        continue;
                    }
                    $root = substr($data, $pos - self::SIZEOF_INT, self::SIZEOF_INT);
                    break;
                }
            }

            if ($root === null) {
                return null;
            }
        }

        list(, $pointer) = unpack('N', $root);

        return $pointer;
    }

    /**
     * @param int $pointer
     *
     * @return array|mixed
     */
    private function node(int $pointer)
    {
        if (!isset($this->nodeCache[$pointer])) {
            while (count($this->nodeCache) + 1 > self::NODE_CACHE_SIZE) {
                array_pop($this->nodeCache);
            }

            if (fseek($this->resource, $pointer, SEEK_SET) === -1) {
                return [null, null];
            }

            if (strlen($data = fread($this->resource, self::SIZEOF_INT)) !== self::SIZEOF_INT) {
                return [null, null];
            }

            list(, $length) = unpack('N', $data);
            if (strlen($node = fread($this->resource, $length)) !== $length) {
                return [null, null];
            }

            $this->nodeCache[$pointer] = self::unserializeNode($node);
        }

        return $this->nodeCache[$pointer];
    }

    /**
     * @param array $configuration
     *
     * @return bool|FileSystemBtreeDriver
     */
    public static function load(array $configuration)
    {
        $fileName = $configuration['file_name'] ?? null;
        if ($fileName === null) {
            return false;
        }

        if (!($resource = @fopen($fileName, 'a+b'))) {
            return false;
        }
        if (($fileName = realpath($fileName)) === false) {
            return false;
        }

        if (fseek($resource, 0, SEEK_END) === -1) {
            fclose($resource);

            return false;
        }
        if (ftell($resource) === 0) {
            if (!flock($resource, LOCK_EX)) {
                fclose($resource);

                return false;
            }
            $root = self::serializeNode(self::NODE_TYPE_VALUE, []);

            $toWrite = pack('N', strlen($root)).$root;
            if (fwrite($resource, $toWrite, strlen($toWrite)) !== strlen($toWrite) ||
                !self::header($resource, 0) || !flock($resource, LOCK_UN)) {
                ftruncate($resource, 0);
                fclose($resource);

                return false;
            }
        }

        return new self($resource, $fileName);
    }

    /**
     * Serialize node.
     *
     * @param string $type
     * @param array  $node
     *
     * @return string
     */
    private static function serializeNode(string $type, array $node)
    {
        return $type.serialize($node);
    }

    /**
     * @param string $serialized
     *
     * @return array
     */
    private static function unserializeNode(string $serialized)
    {
        return [
            substr($serialized, 0, self::SIZEOF_NODE_TYPE),
            unserialize(substr($serialized, self::SIZEOF_NODE_TYPE)),
        ];
    }

    /**
     * @param resource
     * @param int
     *
     * @return bool
     */
    private static function header($resource, $root)
    {
        $toWrite = pack('N', $root).self::HEADER;

        return fwrite($resource, $toWrite, strlen($toWrite)) === strlen($toWrite);
    }
}
