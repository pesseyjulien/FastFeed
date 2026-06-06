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

use FastFeed\Exception\InvalidArgumentException;
use FastFeed\Item;

/**
 * SanitizerProcessor
 * Remove malicious HTML
 */
class SanitizerProcessor implements ProcessorInterface
{
    /**
     * @var \HTMLPurifier
     */
    protected $purifier;

    /**
     * @param string|null $cacheDirectory
     *
     * @throws \FastFeed\Exception\InvalidArgumentException
     */
    public function __construct(?string $cacheDirectory = null)
    {
        if (!$cacheDirectory) {
            $cacheDirectory = realpath(sys_get_temp_dir());
        }

        if (!is_writable($cacheDirectory)) {
            throw new InvalidArgumentException($cacheDirectory.' is not writable');
        }
        // require to configure some CONSTANT
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', $cacheDirectory);
        $this->purifier = new \HTMLPurifier($config);
    }

    /**
     * Execute processor
     *
     * @param array $items
     *
     * @return array
     */
    public function process(array $items): array
    {
        foreach ($items as $key => $item) {
            $items[$key] = $this->doClean($item);
        }

        return $items;
    }

    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function doClean(Item $item): Item
    {
        $item->setIntro(
            $this->purifier->purify($item->getIntro())
        );
        $item->setContent(
            $this->purifier->purify($item->getContent())
        );

        return $item;
    }
}
