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

use DOMDocument;
use DOMElement;

/**
 * AbstractDomParser
 */
abstract class AbstractDomParser
{
    /**
     * @param string $content
     *
     * @return DOMDocument
     * @throws \FastFeed\Exception\RuntimeException
     */
    protected function createDocumentFromXML(string $content): DOMDocument
    {
        $previousValue = libxml_use_internal_errors(true);

        $document = new DOMDocument();
        $document->strictErrorChecking = false;

        // Clean content
        $content = trim($content);
        // Convert to UTF-8 if needed
        $encoding = mb_detect_encoding($content, 'UTF-8, ISO-8859-1, ISO-8859-15', true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        $document->loadXML($content);

        libxml_use_internal_errors($previousValue);

        return $document;
    }

    /**
     * @param DOMElement $node
     * @param string     $tagName
     *
     * @return bool|string
     * @throws \FastFeed\Exception\RuntimeException
     */
    protected function getNodeValueByTagName(DOMElement $node, string $tagName)
    {
        $results = $node->getElementsByTagName($tagName);
        for ($i = 0; $i < $results->length; $i++) {
            $result = $results->item($i);
            if ($result->nodeValue === null || $result->nodeValue === '') {
                continue;
            }

            return $result->nodeValue;
        }

        return false;
    }

    /**
     * @param DOMElement $node
     * @param string     $namespace
     * @param string     $tagName
     *
     * @return bool|string
     * @throws \FastFeed\Exception\RuntimeException
     */
    protected function getNodeValueByTagNameNS(DOMElement $node, string $namespace, string $tagName)
    {
        $results = $node->getElementsByTagNameNS($namespace, $tagName);
        for ($i = 0; $i < $results->length; $i++) {
            $result = $results->item($i);
            if (is_null($result->nodeValue)) {
                continue;
            }

            return $result->nodeValue;
        }

        return false;
    }

    /**
     * @param DOMElement $node
     * @param string     $tagName
     *
     * @return array
     * @throws \FastFeed\Exception\RuntimeException
     */
    protected function getNodeValuesByTagName(DOMElement $node, string $tagName): array
    {
        $values = array();
        $results = $node->getElementsByTagName($tagName);
        if ($results->length) {
            foreach ($results as $result) {
                if ($result->nodeValue) {
                    $values[] = $result->nodeValue;
                }
            }
        }

        return $values;
    }

    /**
     * @param DOMElement $node
     * @param string     $tagName
     * @param string     $propertyName
     *
     * @return array
     * @throws \FastFeed\Exception\RuntimeException
     */
    protected function getNodePropertyByTagName(\DOMElement $node, string $tagName, string $propertyName): array
    {
        $values = array();
        $results = $node->getElementsByTagName($tagName);
        if ($results->length) {
            foreach ($results as $result) {
                if ($result->getAttribute($propertyName)) {
                    $values[] = $result->getAttribute($propertyName);
                }
            }
        }

        return $values;
    }
}
