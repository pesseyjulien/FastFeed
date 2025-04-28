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

use FastFeed\FastFeed;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * AbstractFeedManagerTest
 */
abstract class AbstractFastFeedTest extends TestCase
{
    /**
     * @var FastFeed
     */
    protected $fastFeed;

    /**
     * @var LoggerInterface
     */
    protected $loggerMock;

    /**
     * @var ClientInterface
     */
    protected $httpMock;

    public function setUp(): void
    {
        $this->httpMock = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fastFeed = new FastFeed($this->httpMock, $this->loggerMock);

        parent::setUp();
    }
}
