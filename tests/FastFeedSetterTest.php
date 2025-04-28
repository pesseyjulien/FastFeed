<?php
/**
 * This file is part of the FastFeed package.
 *
 * (c) Daniel González <daniel@desarrolla2.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FastFeed\Tests;

use FastFeed\Parser\ParserInterface;
use FastFeed\Processor\ProcessorInterface;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * FastFeedSetterTest
 */
class FastFeedSetterTest extends AbstractFastFeedTest
{
    public function testSetGuzzle()
    {
        $guzzleMock = $this->createMock(ClientInterface::class);
        $this->assertNull($this->fastFeed->setHttpClient($guzzleMock));
    }

    public function testSetLogger()
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->assertNull($this->fastFeed->setLogger($loggerMock));
    }

    public function testPushParser()
    {
        $parserMock = $this->createMock(ParserInterface::class);
        $this->assertNull($this->fastFeed->pushParser($parserMock));
    }

    public function testPopParser()
    {
        $parserMock = $this->createMock(ParserInterface::class);
        $this->fastFeed->pushParser($parserMock);
        $this->assertInstanceOf(ParserInterface::class, $this->fastFeed->popParser());
    }

    public function testPushProcessor()
    {
        $processorMock = $this->createMock(ProcessorInterface::class);
        $this->assertNull($this->fastFeed->pushProcessor($processorMock));
    }

    public function testPopProcessor()
    {
        $processorMock = $this->createMock(ProcessorInterface::class);
        $this->fastFeed->pushProcessor($processorMock);
        $this->assertInstanceOf(ProcessorInterface::class, $this->fastFeed->popProcessor());
    }
}
