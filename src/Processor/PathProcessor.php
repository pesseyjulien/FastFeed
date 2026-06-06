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
use FastFeed\Url\Url;

/**
 * PathProcessor
 */
class PathProcessor implements ProcessorInterface
{
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
            $items[$key] = $this->fixPaths($item);
        }

        return $items;
    }

    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function fixPaths(Item $item): Item
    {
        $url = new Url($item->getSource());
        $item->setIntro($this->getFixedText($item->getIntro(), $url->getFullHost()));
        $item->setContent($this->getFixedText($item->getContent(), $url->getFullHost()));

        return $item;
    }

    /**
     * @param string $text
     * @param string $domain
     *
     * @return string
     */
    protected function getFixedText(string $text, string $domain): string
    {
        $text = str_ireplace('href="/', 'href="'.$domain.'/', $text);
        $text = str_ireplace('href=\'/', 'href=\''.$domain.'/', $text);
        $text = str_ireplace('src="/', 'src="'.$domain.'/', $text);
        $text = str_ireplace('src=\'/', 'src=\''.$domain.'/', $text);

        return $text;
    }
}
