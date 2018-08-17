<?php

namespace Algm\LargeXmlReader\Tests;

use Algm\LargeXmlReader\Xml\Reader;
use PHPUnit\Framework\TestCase;

class XmlProcessTest extends TestCase
{
    public function testCyclesOnlySomeElements()
    {
        $timesCalled = 0;
        $reader = Reader::openStream($this->openXmlFile(), 3);

        $reader->process(function () use (&$timesCalled) {
            $timesCalled++;
        }, 2);

        $this->assertEquals(2, $timesCalled);
    }

    public function testCyclesThroughUniqueNodes()
    {
        $timesCalled = 0;
        $reader = Reader::openUniqueNodeStream($this->openXmlFile(), 'item');

        $reader->process(function () use (&$timesCalled) {
            $timesCalled++;
        }, 10);

        $this->assertEquals(10, $timesCalled);
    }

    public function testGetsTheNodeData()
    {
        $timesCalled = 0;
        $item = null;
        $reader = Reader::openUniqueNodeStream($this->openXmlFile(), 'item');
        $expected = array(
            'item' => array(
                '_attributes' => array(
                    'id' => 'item0',
                ),
                'location' => 'United States',
                'quantity' => 1,
                'name' => 'duteous nine eighteen ',
                'payment' => 'Creditcard',
                'description' => array(
                    'parlist' => array(
                        'listitem' => array(
                            'text' => '
shepherd noble supposed dotage humble servilius bitch theirs venus dismal wounds gum merely raise red breaks earth god folds closet captain dying reek ' . '
',
                        ),
                    ),
                ),
                'shipping' => 'Will ship internationally, See description for charges',
                'incategory' => array(
                    '_attributes' => array(
                        'category' => 'category12',
                    ),
                ),
                'mailbox' => array(
                    'mail' => array(
                        'from' => 'Libero Rive mailto:Rive@hitachi.com',
                        'to' => 'Benedikte Glew mailto:Glew@sds.no',
                        'date' => '08/05/1999',
                        'text' => '
virgin preventions half logotype weapons granted factious already carved fretted impress pestilent ',
                    ),
                ),
            ),
        );

        $reader->process(function (array $data) use (&$timesCalled, &$item) {
            $timesCalled++;
            $item = $data;
        }, 1);

        $this->assertEquals($expected, $item);
    }

    public function testCyclesThroughBigXmlWithoutCrashing()
    {
        $timesCalled = 0;
        $reader = Reader::openStream($this->openXmlFile(), 3);

        $reader->process(function () use (&$timesCalled) {
            $timesCalled++;
        });

        $this->assertEquals(4, $timesCalled);
    }

    protected function openXmlFile()
    {
        $filename = realpath(dirname(__DIR__) . '/fixtures/standard.xml');

        return fopen($filename, 'r');
    }
}
