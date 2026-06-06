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

use FastFeed\Item;
use FastFeed\Parser\AtomParser;

/**
 * AtomFirstElementParserTest
 */
class AtomParserFirstElementTest extends AbstractAtomParserTest
{
    public function setUp(): void
    {
        $this->parser = new AtomParser();
    }

    public function dataProvider()
    {
        $data = array();

        foreach ($this->xmls as $xml) {
            $data[] = array(
                $xml,
            );
        }

        return $data;
    }

    /**
     * @dataProvider dataProvider
     */
    public function testId($fileName)
    {
        $content = $this->getContent($fileName);
        $item = $this->getItem($content);

        $expected = $this->getFirstValueFromXpath($content, "*/ns:id[1]");

        $this->assertEquals(
            $expected,
            $item->getId(),
            'Fail asserting that first element of '.$fileName.' has id "'.$expected.'"'
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testName($fileName)
    {
        $content = $this->getContent($fileName);
        $item = $this->getItem($content);

        $expected = $this->getFirstValueFromXpath($content, "*/ns:title[1]");

        $this->assertEquals(
            $expected,
            $item->getName(),
            'Fail asserting that first element of '.$fileName.' has name "'.$expected.'"'
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testIntro($fileName)
    {
        $content = $this->getContent($fileName);
        $item = $this->getItem($content);

        $expected = $this->getFirstValueFromXpath($content, "*/ns:content[1]");

        $this->assertEquals(
            $expected,
            $item->getIntro(),
            'Fail asserting that first element of '.$fileName.' has intro "'.$expected.'"'
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testContent($fileName)
    {
        $content = $this->getContent($fileName);
        $item = $this->getItem($content);

        $expected = $this->getFirstValueFromXpath($content, "*/ns:content[1]");

        $this->assertEquals(
            $expected,
            $item->getContent(),
            'Fail asserting that first element of '.$fileName.' has content "'.$expected.'"'
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSource($fileName)
    {
        $content = $this->getContent($fileName);
        $item = $this->getItem($content);

        $expected = $this->getFistAttributeFromXpath(
            $content,
            "*/ns:link[@rel='".AtomParser::SOURCE_LINK_ATTR."']",
            'href'
        );

        $this->assertEquals(
            $expected,
            $item->getSource(),
            'Fail asserting that first element of '.$fileName.' has source "'.$expected.'"'
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAuthor($fileName)
    {
        $content = $this->getContent($fileName);
        $item = $this->getItem($content);

        $expected = $this->getFirstValueFromXpath($content, "*/ns:email");

        $this->assertEquals(
            $expected,
            $item->getAuthor(),
            'Fail asserting that first element of '.$fileName.' has author "'.$expected.'"'
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testImage($fileName)
    {
        $content = $this->getContent($fileName);
        $item = $this->getItem($content);

        $dom = new \DOMDocument();
        @$dom->loadXML(trim($content));
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('ns', 'http://www.w3.org/2005/Atom');
        $xpath->registerNamespace('media', 'http://search.yahoo.com/mrss/');
        
        $expected = '';
        
        $enclosures = $xpath->query("//ns:entry[1]/ns:link[@rel='enclosure']");
        if ($enclosures->length) {
            $type = $enclosures->item(0)->getAttribute('type');
            $href = $enclosures->item(0)->getAttribute('href');
            if ($href && (strpos($type, 'image/') === 0 || preg_match('/\.(jpg|jpeg|png|gif|webp|svg)/i', $href))) {
                $expected = $href;
            }
        }
        
        if (!$expected) {
            $mediaThumbnails = $xpath->query("//ns:entry[1]/media:thumbnail");
            if ($mediaThumbnails->length) {
                $expected = $mediaThumbnails->item(0)->getAttribute('url');
            }
        }

        $this->assertEquals(
            $expected,
            $item->getImage(),
            'Fail asserting that first element of '.$fileName.' has image "'.$expected.'"'
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDate($fileName)
    {
        $content = $this->getContent($fileName);
        $item = $this->getItem($content);

        $expected = strtotime($this->getFirstValueFromXpath($content, "*/ns:published"));

        $this->assertEquals(
            $expected,
            $item->getDate()->getTimestamp(),
            'Fail in assert of first element date of '.$fileName.'  '
        );
    }

    protected function getContent($xml)
    {
        return file_get_contents(__DIR__.$this->path.$xml);
    }

    protected function getItem($content)
    {
        $nodes = $this->parser->getNodes($content);

        return array_shift($nodes);
    }
}
