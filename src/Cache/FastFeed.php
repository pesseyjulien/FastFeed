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

namespace FastFeed\Cache;

use FastFeed\Exception\LogicException;
use FastFeed\FastFeed as FastFeedBase;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * FastFeed
 */
class FastFeed extends FastFeedBase
{
    /**
     * @param string $channel
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function fetch(string $channel = 'default'): array
    {
        $items = $this->getFromCache($channel);
        if (!$items) {
            $items = parent::fetch($channel);
            $this->setToCache($channel, $items);
        }

        return $items;
    }

    /**
     * @param string $channel
     *
     * @return ?array
     * @throws LogicException|InvalidArgumentException
     */
    protected function getFromCache(string $channel): ?array
    {
        if (!$this->cache) {
            throw new LogicException('You need set to cache provider');
        }
        if ($this->getCache()->has($channel)) {
            return $this->getCache()->get($channel);
        }

        return null;
    }

    /**
     * @param string $channel
     * @param array $items
     * @throws InvalidArgumentException
     */
    protected function setToCache(string $channel, array $items): void
    {
        $this->getCache()->set($channel, $items);
    }
}
