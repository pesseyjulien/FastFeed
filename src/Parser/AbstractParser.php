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

use DOMElement;

use FastFeed\Aggregator\AggregatorInterface;
use FastFeed\Exception\LogicException;
use FastFeed\Item;

/**
 * AbstractParser
 */
abstract class AbstractParser extends AbstractDomParser
{
    /**
     * @var array
     */
    protected $aggregators = array();

    /**
     * @return AggregatorInterface
     * @throws \FastFeed\Exception\LogicException
     */
    public function popAggregator(): AggregatorInterface
    {
        if (!$this->aggregators) {
            throw new LogicException('You tried to pop from an empty Aggregator stack.');
        }

        return array_shift($this->aggregators);
    }

    /**
     * @param AggregatorInterface $aggregator
     */
    public function pushAggregator(AggregatorInterface $aggregator): void
    {
        $this->aggregators[] = $aggregator;
    }

    /**
     * @param DOMElement $node
     * @param Item       $item
     */
    protected function executeAggregators(DOMElement $node, Item $item): void
    {
        foreach ($this->aggregators as $aggregator) {
            $aggregator->process($node, $item);
        }
    }

    /**
     * @return array
     */
    abstract protected function getPropertiesMapping(): array;

    /**
     * @param DOMElement $node
     * @param Item       $item
     */
    protected function setProperties(DOMElement $node, Item $item): void
    {
        $propertiesMapping = $this->getPropertiesMapping();
        foreach ($propertiesMapping as $methodName => $propertyName) {
            $value = $this->getNodeValueByTagName($node, $propertyName);
            if ($value !== false && $value !== '') {
                $item->$methodName($value);
            }
        }
    }
}
