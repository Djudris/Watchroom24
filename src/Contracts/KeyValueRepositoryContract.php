<?php

namespace KALMARS\Contracts;

use KALMARS\Models\KeyValue;

/**
 * Class KeyValueRepositoryContract
 * @package KALMARS\Contracts
 */
interface KeyValueRepositoryContract
{
    /**
     * Set Item
     *
     * @param string $key
     * @param array $value
     * @return KeyValue
     */
    public function setItem($key, $value): KeyValue;

    /**
     * Get item
     *
     * @param string $key
     * @return array
     */
    public function getItem($key): array;
}
