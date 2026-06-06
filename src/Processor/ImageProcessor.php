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
 * ImageProcessor
 */
class ImageProcessor extends ImagesProcessor
{
    /**
     * @var bool
     */
    protected $overrideImage = false;

    /**
     * @param bool $overrideImage
     */
    public function setOverrideImage(bool $overrideImage): void
    {
        $this->overrideImage = $overrideImage;
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
            $items[$key] = $this->setImage($item);
        }

        return $items;
    }

    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function setImage(Item $item): Item
    {
        if ($item->hasImage() && !$this->overrideImage) {
            return $item;
        }

        $this->setImageFromContent($item);

        return $item;
    }

    /**
     * @param Item $item
     */
    protected function setImageFromContent(Item $item): void
    {
        $images = $this->getImages($item->getContent());

        foreach ($images as $image) {
            if ($this->isOnIgnoredPatterns($image)) {
                continue;
            }
            $item->setImage($image);

            return;
        }
    }
}
