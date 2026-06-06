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
 * RSSParserTest
 */
class RSSParserTest extends AbstractRSSParserTest
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
        $expectedNodes = substr_count($content, '<item>');
        $this->assertCount(
            $expectedNodes,
            $nodes,
            'Fail asserting that '.$fileName.' has '.$expectedNodes.' nodes'
        );
    }

    public function testZeroValues()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
  <channel>
    <item>
      <title>0</title>
      <description>0</description>
      <link>http://example.com/0</link>
    </item>
  </channel>
</rss>';
        $nodes = $this->parser->getNodes($xml);
        $this->assertCount(1, $nodes);
        $item = $nodes[0];
        $this->assertEquals('0', $item->getName());
        $this->assertEquals('0', $item->getContent());
    }

    public function testMediaEnclosureImages()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
  <channel>
    <item>
      <title>Test Enclosure</title>
      <link>http://example.com/enclosure</link>
      <enclosure url="http://example.com/image.jpg" type="image/jpeg" length="12345"/>
    </item>
  </channel>
</rss>';
        $nodes = $this->parser->getNodes($xml);
        $this->assertCount(1, $nodes);
        $item = $nodes[0];
        $this->assertEquals('http://example.com/image.jpg', $item->getImage());
    }

    public function testMediaContentImages()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <item>
      <title>Test Media Content</title>
      <link>http://example.com/media</link>
      <media:content url="http://example.com/media.png" medium="image"/>
    </item>
  </channel>
</rss>';
        $nodes = $this->parser->getNodes($xml);
        $this->assertCount(1, $nodes);
        $item = $nodes[0];
        $this->assertEquals('http://example.com/media.png', $item->getImage());
    }
}
