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

/**
 * FeedManagerExceptionTest
 */
class FeedManagerExceptionTest extends AbstractFastFeedTest
{
    /**
     * @return array
     */
    public function dataProviderForAddFeed()
    {
        return array(
            array(1, 1), // invalid channel
            array('default', 1), // invalid url
            array('default', 'invalid url'), // invalid url
        );
    }

    /**
     * @dataProvider dataProviderForAddFeed
     */
    public function testAddFeed($channel, $feed)
    {
        $this->expectException(\FastFeed\Exception\LogicException::class);
        $this->fastFeed->addFeed($channel, $feed);
    }

    /**
     * @dataProvider dataProviderForAddFeed
     */
    public function testSetFeed($channel, $feed)
    {
        $this->expectException(\FastFeed\Exception\LogicException::class);
        $this->fastFeed->setFeed($channel, $feed);
    }

    public function testGetFeed()
    {
        $this->expectException(\FastFeed\Exception\LogicException::class);
        $this->fastFeed->getFeed('this channel no exist');
    }

    public function testPopParser()
    {
        $this->expectException(\FastFeed\Exception\LogicException::class);
        $this->fastFeed->popParser();
    }

    public function testPopProcessor()
    {
        $this->expectException(\FastFeed\Exception\LogicException::class);
        $this->fastFeed->popProcessor();
    }

    public function testFetch()
    {
        $this->expectException(\FastFeed\Exception\LogicException::class);
        $this->fastFeed->fetch(34);
    }
}
