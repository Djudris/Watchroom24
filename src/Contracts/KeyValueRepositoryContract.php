<?php

namespace Legend\Contracts;

use Legend\Models\KeyValue;

/**
 * Class KeyValueRepositoryContract
 * @package Legend\Contracts
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
