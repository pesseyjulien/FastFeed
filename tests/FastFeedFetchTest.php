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

use FastFeed\Parser\RSSParser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * FastFeedFetchTest
 */
class FastFeedFetchTest extends AbstractFastFeedTest
{
    public function dataProvider(): array
    {
        return array(
            [''],
            ['nothing here'],
            [file_get_contents(__DIR__.'/data/rss20/desarrolla2.com.xml')],
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFetch($content)
    {
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
            ->willReturn($content);

        $this->fastFeed->addFeed('desarrolla2', 'http://desarrolla2.com/feed/');
        $this->fastFeed->pushParser(new RSSParser());
        $this->fastFeed->fetch('desarrolla2');
    }

    public function testFetchWithConditionalGet304()
    {
        $cacheMock = $this->createMock(\Desarrolla2\Cache\CacheInterface::class);
        $cacheMock->expects($this->once())
            ->method('has')
            ->willReturn(true);
        $cacheMock->expects($this->once())
            ->method('get')
            ->willReturn([
                'etag' => '"12345"',
                'last_modified' => 'Wed, 21 Oct 2015 07:28:00 GMT',
                'content' => 'cached-xml-content'
            ]);

        $this->fastFeed->setCache($cacheMock);

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpMock->expects($this->once())
            ->method('request')
            ->with('GET', 'http://desarrolla2.com/feed/', $this->callback(function ($options) {
                return isset($options['headers']['If-None-Match']) &&
                       $options['headers']['If-None-Match'] === '"12345"' &&
                       isset($options['headers']['If-Modified-Since']) &&
                       $options['headers']['If-Modified-Since'] === 'Wed, 21 Oct 2015 07:28:00 GMT';
            }))
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(304);

        $parserMock = $this->createMock(\FastFeed\Parser\ParserInterface::class);
        $parserMock->expects($this->once())
            ->method('getNodes')
            ->with('cached-xml-content')
            ->willReturn([]);

        $this->fastFeed->addFeed('desarrolla2', 'http://desarrolla2.com/feed/');
        $this->fastFeed->pushParser($parserMock);
        $this->fastFeed->fetch('desarrolla2');
    }
}
