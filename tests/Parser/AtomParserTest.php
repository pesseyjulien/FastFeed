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

/**
 * AtomParserTest
 */
class AtomParserTest extends AbstractAtomParserTest
{
    public function dataProvider()
    {
        $data = array();

        foreach ($this->xmls as $xml) {
            $content = file_get_contents(__DIR__.$this->path.$xml);
            $data[] = array(
                $content,
                $xml,
            );
        }

        return $data;
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCountNodes($content, $fileName)
    {
        $nodes = $this->parser->getNodes($content);
        $expectedNodes = substr_count($content, '<entry>');
        $this->assertCount(
            $expectedNodes,
            $nodes,
            'Fail asserting that '.$fileName.' has '.$expectedNodes.' nodes'
        );
    }

    public function testFallbackDate()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <entry>
    <id>1</id>
    <title>Test Title</title>
    <updated>2026-06-04T12:00:00Z</updated>
  </entry>
</feed>';
        $nodes = $this->parser->getNodes($xml);
        $this->assertCount(1, $nodes);
        $item = $nodes[0];
        $this->assertInstanceOf('FastFeed\Item', $item);
        $this->assertNotFalse($item->getDate());
        $this->assertEquals(strtotime('2026-06-04T12:00:00Z'), $item->getDate()->getTimestamp());
    }

    public function testMediaEnclosureLink()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <entry>
    <id>1</id>
    <title>Test Enclosure</title>
    <link rel="enclosure" type="image/png" href="http://example.com/atom-image.png"/>
  </entry>
</feed>';
        $nodes = $this->parser->getNodes($xml);
        $this->assertCount(1, $nodes);
        $item = $nodes[0];
        $this->assertEquals('http://example.com/atom-image.png', $item->getImage());
    }
}
