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

use DOMDocument;

use FastFeed\Item;

/**
 * ImagesProcessor
 */
class ImagesProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    protected $ignoredPatterns = array();

    /**
     * @param array $ignoredPatterns
     */
    public function setIgnoredPatterns(array $ignoredPatterns): void
    {
        $this->ignoredPatterns = array();
        foreach ($ignoredPatterns as $ignoredPattern) {
            $this->addIgnoredPattern($ignoredPattern);
        }
    }

    /**
     * @param string $ignoredPattern
     */
    public function addIgnoredPattern(string $ignoredPattern): void
    {
        $this->ignoredPatterns[] = $ignoredPattern;
    }

    /**
     * @return array
     */
    public function getIgnoredPatterns(): array
    {
        return $this->ignoredPatterns;
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
            $items[$key] = $this->setImages($item);
        }

        return $items;
    }

    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function setImages(Item $item): Item
    {
        $this->setImagesFromContent($item);

        return $item;
    }

    /**
     * @param Item $item
     */
    protected function setImagesFromContent(Item $item): void
    {
        $allImages = $this->getImages($item->getContent());
        $images = array();

        foreach ($allImages as $image) {
            if ($this->isOnIgnoredPatterns($image)) {
                continue;
            }
            $images[$image] = $image;
        }

        $item->setExtra('images', array_values($images));
    }

    /**
     * @param string $imageSrc
     *
     * @return bool
     */
    protected function isOnIgnoredPatterns(string $imageSrc): bool
    {
        foreach ($this->ignoredPatterns as $ignoredPattern) {
            if (preg_match($ignoredPattern, $imageSrc)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $content
     *
     * @return DOMDocument|bool
     */
    protected function createDOM(string $content)
    {
        if (!$content) {
            return false;
        }
        $dom = new DOMDocument();
        $dom->loadHTML($content);
        $dom->preserveWhiteSpace = false;

        return $dom;
    }

    /**
     * @param string $content
     *
     * @return array
     */
    protected function getImages(string $content): array
    {
        $images = array();

        $dom = $this->createDOM($content);
        if (!$dom) {
            return $images;
        }

        $domList = $dom->getElementsByTagName('img');

        foreach ($domList as $image) {
            $imageSrc = $image->getAttribute('src');

            if (!$imageSrc) {
                continue;
            }
            $images[] = $imageSrc;
        }

        return $images;
    }
}
