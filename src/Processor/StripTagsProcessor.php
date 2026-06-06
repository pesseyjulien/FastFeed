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

use FastFeed\Item;

/**
 * StripTagsProcessor
 */
class StripTagsProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    protected $allowedTags = array('content' => '', 'intro' => '');

    /**
     * @param string $allowedTags
     */
    public function setAllowedTagsForContent(string $allowedTags): void
    {
        $this->allowedTags['content'] = $allowedTags;
    }

    /**
     * @param string $allowedTags
     */
    public function setAllowedTagsForIntro(string $allowedTags): void
    {
        $this->allowedTags['intro'] = $allowedTags;
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
        $item->setIntro(strip_tags($item->getIntro(), $this->allowedTags['intro']));
        $item->setContent(strip_tags($item->getContent(), $this->allowedTags['content']));

        return $item;
    }
}
