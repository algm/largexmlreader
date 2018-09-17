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
        $expected = json_decode(json_encode(array(
            '@attributes' => array(
                'id' => 'item0',
            ),
            'location' => 'United States',
            'quantity' => '1',
            'name' => 'duteous nine eighteen ',
            'payment' => 'Creditcard',
            'description' => array(
                'parlist' => array(
                    'listitem' => array(
                        array(
                            'text' => " \npage rous lady idle authority capt professes stabs monster petition heave humbly removes rescue runs shady peace most piteous worser oak assembly holes patience but malice whoreson mirrors master tenants smocks yielded  \n",

                        ),
                        array('text' => "\nshepherd noble supposed dotage humble servilius bitch theirs venus dismal wounds gum merely raise red breaks earth god folds closet captain dying reek \n",
                        ),
                    ),
                ),
            ),
            'shipping' => 'Will ship internationally, See description for charges',
            'incategory' => [
                [
                    '@attributes' => [
                        'category' => 'category540',
                    ],
                ],
                [
                    '@attributes' => [
                        'category' => 'category418',
                    ],
                ],
                [
                    '@attributes' => [
                        'category' => 'category985',
                    ],
                ],
                [
                    '@attributes' => [
                        'category' => 'category787',
                    ],
                ],
                [
                    '@attributes' => [
                        'category' => 'category12',
                    ],
                ],
            ],
            'mailbox' => array(
                'mail' => array(
                    'from' => 'Libero Rive mailto:Rive@hitachi.com',
                    'to' => 'Benedikte Glew mailto:Glew@sds.no',
                    'date' => '08/05/1999',
                    'text' => "\nvirgin preventions half logotype weapons granted factious already carved fretted impress pestilent  discomfort sinful conceiv corn preventions greatly suit observe sinews enforcement  gold gazing set almost catesby turned servilius cook doublet preventions shrunk smooth great choice enemy disguis tender might deceit ros dreadful stabbing fold unjustly ruffian life hamlet containing leaves \n",
                ),
            ),
        )), true);

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
