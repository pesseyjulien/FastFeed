<?php
/**
 * This file is part of the FastFeed package.
 *
 * (c) Daniel González <daniel@desarrolla2.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FastFeed\Tests\Parser;

use FastFeed\Aggregator\AggregatorInterface;
use FastFeed\Parser\RSSParser;
use PHPUnit\Framework\TestCase;

/**
 * ParserManagerTest
 */
class ParserSetterTest extends TestCase
{
    /**
     * @var RSSParser
     */
    protected $parser;

    public function setUp(): void
    {
        $this->parser = new RSSParser();
    }

    public function testPushProcessor()
    {
        $aggregatorMock = $this->createMock(AggregatorInterface::class);
        $this->assertNull($this->parser->pushAggregator($aggregatorMock));
    }

    public function testPopParser()
    {
        $aggregatorMock = $this->createMock(AggregatorInterface::class);
        $this->parser->pushAggregator($aggregatorMock);
        $this->assertInstanceOf(
            AggregatorInterface::class,
            $this->parser->popAggregator()
        );
    }

    public function testPopProcessor()
    {
        $this->expectException(\FastFeed\Exception\LogicException::class);
        $this->parser->popAggregator();
    }
}
