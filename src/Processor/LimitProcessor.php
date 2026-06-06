<?php

declare(strict_types=1);

/**
 * This file is part of the FastFeed package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 */

namespace FastFeed\Processor;

/**
 * LimitProcessor
 * Set the max number of items in result set
 */
class LimitProcessor implements ProcessorInterface
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @param int $limit
     */
    public function __construct(int $limit)
    {
        $this->setLimit($limit);
    }

    /**
     * Set the max number of items in result set
     *
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @param array $items
     *
     * @return array
     */
    public function process(array $items): array
    {
        if (!$this->limit) {
            return $items;
        }

        return array_slice($items, 0, $this->limit);
    }
}
