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

namespace FastFeed\Parser;

use DateTime;
use DOMElement;

use FastFeed\Exception\RuntimeException;
use FastFeed\Item;

/**
 * RSSParser
 */
class RSSParser extends AbstractParser implements ParserInterface
{
    /**
     * Retrieve a Items's array
     *
     * @param string $content
     *
     * @return array
     * @throws \FastFeed\Exception\RuntimeException
     */
    public function getNodes(string $content): array
    {
        $items = array();
        $document = $this->createDocumentFromXML($content);
        $nodes = $document->getElementsByTagName('item');
        if ($nodes->length) {
            foreach ($nodes as $node) {
                try {
                    $item = $this->create($node);
                    $items[] = $item;
                } catch (\Exception $e) {
                    throw new RuntimeException($e->getMessage());
                }
            }
        }

        return $items;
    }

    /**
     * @param DOMElement $node
     *
     * @return Item
     */
    public function create(DOMElement $node): Item
    {
        $item = new Item();
        $this->setProperties($node, $item);
        $this->setDate($node, $item);
        $this->setTags($node, $item);
        $this->setMediaImages($node, $item);
        $this->executeAggregators($node, $item);

        return $item;
    }

    /**
     * @return array
     */
    protected function getPropertiesMapping(): array
    {
        return array(
            'setId' => 'link',
            'setName' => 'title',
            'setIntro' => 'description',
            'setContent' => 'description',
            'setSource' => 'link',
            'setAuthor' => 'author',
        );
    }

    /**
     * @param DOMElement $node
     * @param Item       $item
     */
    protected function setDate(DOMElement $node, Item $item): void
    {
        $value = $this->getNodeValueByTagName($node, 'pubDate');
        if ($value) {
            if (strtotime($value)) {
                $item->setDate(new DateTime($value));
            }
        }
    }

    /**
     * @param DOMElement $node
     * @param Item       $item
     */
    protected function setTags(DOMElement $node, Item $item): void
    {
        $tags = $this->getNodeValuesByTagName($node, 'category');
        foreach ($tags as $tag) {
            $item->addTag($tag);
        }
    }

    /**
     * Parse enclosures and media contents to set default image
     *
     * @param DOMElement $node
     * @param Item       $item
     */
    protected function setMediaImages(DOMElement $node, Item $item): void
    {
        $enclosures = $node->getElementsByTagName('enclosure');
        foreach ($enclosures as $enclosure) {
            $type = $enclosure->getAttribute('type');
            $url = $enclosure->getAttribute('url');
            if ($url && (strpos($type, 'image/') === 0 || preg_match('/\.(jpg|jpeg|png|gif|webp|svg)/i', $url))) {
                $item->setImage($url);
                return;
            }
        }

        $mediaContents = $node->getElementsByTagNameNS('http://search.yahoo.com/mrss/', 'content');
        if (!$mediaContents->length) {
            $mediaContents = $node->getElementsByTagName('media:content');
        }
        foreach ($mediaContents as $mediaContent) {
            $url = $mediaContent->getAttribute('url');
            $medium = $mediaContent->getAttribute('medium');
            if ($url && ($medium === 'image' || preg_match('/\.(jpg|jpeg|png|gif|webp|svg)/i', $url))) {
                $item->setImage($url);
                return;
            }
        }

        $thumbnails = $node->getElementsByTagNameNS('http://search.yahoo.com/mrss/', 'thumbnail');
        if (!$thumbnails->length) {
            $thumbnails = $node->getElementsByTagName('media:thumbnail');
        }
        if (!$thumbnails->length) {
            $thumbnails = $node->getElementsByTagName('thumbnail');
        }
        foreach ($thumbnails as $thumbnail) {
            $url = $thumbnail->getAttribute('url');
            if ($url) {
                $item->setImage($url);
                return;
            }
        }
    }
}
