<?php
/**
 * This file is part of the planetubuntu package.
 *
 * (c) Daniel González <daniel@desarrolla2.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FastFeed\Tests\Processor;

use FastFeed\Processor\StripTagsProcessor;
use FastFeed\Item;
use PHPUnit\Framework\TestCase;

/**
 * StripTagsProcessorTest
 */
class StripTagsProcessorTest extends TestCase
{
    /**
     * @var StripTagsProcessor
     */
    protected $processor;

    /**
     * @var array
     */
    protected $items;

    public function setUp(): void
    {
        $this->processor = new StripTagsProcessor();
        $this->items = array(new Item());
    }

    /**
     * Provider
     *
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array('hi', '<p>hi</p>', ''),
            array('<p>hi</p>', '<p>hi</p>', '<p>'),
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testProcess($expected, $actual, $allowedTags)
    {
        $this->items[0]->setIntro($actual);
        $this->items[0]->setContent($actual);
        $this->processor->setAllowedTagsForContent($allowedTags);
        $this->processor->setAllowedTagsForIntro($allowedTags);
        $this->items = $this->processor->process($this->items);
        $this->assertEquals($expected, $this->items[0]->getIntro());
        $this->assertEquals($expected, $this->items[0]->getContent());
    }

    public function testProcessDefault()
    {
        $this->items[0]->setIntro('<p>hi</p>');
        $this->items[0]->setContent('<p>hi</p>');
        $this->items = $this->processor->process($this->items);
        $this->assertEquals('hi', $this->items[0]->getIntro());
        $this->assertEquals('hi', $this->items[0]->getContent());
    }
}
