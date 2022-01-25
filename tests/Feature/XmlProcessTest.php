<?php

namespace Algm\LargeXmlReader\Tests\Feature;

use Algm\LargeXmlReader\Xml\Reader;
use Exception;
use PHPUnit\Framework\TestCase;

class XmlProcessTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCyclesOnlySomeElements()
    {
        $timesCalled = 0;
        $reader = Reader::openStream($this->openXmlFile(), 3);

        $iterator = $reader->process(2);

        foreach ($iterator as $ignored) {
            $timesCalled++;
        }

        $this->assertEquals(2, $timesCalled);
    }

    /**
     * @throws Exception
     */
    public function testCyclesThroughUniqueNodes()
    {
        $timesCalled = 0;
        $reader = Reader::openUniqueNodeStream($this->openXmlFile(), 'item');

        $iterator = $reader->process();

        foreach ($iterator as $ignored) {
            $timesCalled++;
        }

        $this->assertEquals(4, $timesCalled);
    }

    /**
     * @throws Exception
     */
    public function testGetsTheNodeData()
    {
        $reader = Reader::openUniqueNodeStream($this->openXmlFile('single.xml'), 'item');

        $iterator = $reader->process(1);

        foreach ($iterator as $item) {
            $this->assertEquals('Anashria Womens Premier Leather Sandal', $item['name']);
        }
    }

    /**
     * @return false|resource
     * @noinspection PhpMissingReturnTypeInspection
     */
    protected function openXmlFile(string $file = 'standard.xml')
    {
        $filename = realpath(dirname(__DIR__) . "/fixtures/$file");

        return fopen($filename, 'r');
    }
}
