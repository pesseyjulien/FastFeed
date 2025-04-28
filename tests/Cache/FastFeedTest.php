<?php
/**
 * This file is part of the planetubuntu package.
 *
 * (c) Daniel González <daniel@desarrolla2.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FastFeed\Tests\Cache;

use FastFeed\Tests\AbstractFastFeedTest;
use FastFeed\Cache\FastFeed;
use FastFeed\Item;
use Desarrolla2\Cache\CacheInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * FastFeedTest
 */
class FastFeedTest extends AbstractFastFeedTest
{
    /**
     * @var CacheInterface
     */
    protected $cacheMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fastFeed = new FastFeed($this->httpMock, $this->loggerMock);
        $this->fastFeed->setCache($this->cacheMock);
        $this->fastFeed->addFeed('desarrolla2', 'http://desarrolla2.com/feed/');
    }

    public function testGetCache()
    {
        $this->assertInstanceOf(CacheInterface::class, $this->fastFeed->getCache());
    }

    public function testWithCache()
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true));

        $this->cacheMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue(array(new Item())));

        $this->fastFeed->fetch('desarrolla2');
    }

    public function testWithOutCache()
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));

        $this->cacheMock
            ->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpMock->expects($this->once())
            ->method('request')
            ->will($this->returnValue($responseMock));

        $responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($stream = $this->createMock(StreamInterface::class));

        $stream->expects($this->once())
            ->method('getContents')
            ->willReturn('content');

        $this->fastFeed->fetch('desarrolla2');
    }
}
