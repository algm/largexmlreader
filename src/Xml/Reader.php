<?php

namespace Algm\LargeXmlReader\Xml;

use Exception;
use Prewk\XmlStringStreamer;
use Vyuldashev\XmlToArray\XmlToArray;

class Reader
{
    /**
     * XmlReader Instance
     *
     * @var XmlReader
     */
    protected $xml;

    /**
     * Constructor
     *
     * @param \Prewk\XmlStringStreamer $xml
     */
    public function __construct(XmlStringStreamer $xml)
    {
        $this->xml = $xml;
    }

    /**
     * Returns an instance of the reader from the passed stream.
     * You may setup the depth of the nodes you want to iterate by
     * using the second parameter (defaults to 2).
     *
     * @param resource $stream
     * @param int $depth
     * @return self
     */
    public static function openStream($stream, int $depth = 2): self
    {
        if (!is_resource($stream)) {
            throw new Exception('Invalid resource passed to openStream');
        }

        $xml = XmlStringStreamer::createStringWalkerParser($stream, [
            'captureDepth' => $depth,
        ]);

        return new Reader($xml);
    }

    /**
     * Returns an instance of the reader from the passed stream that
     * iterates throug the nodes that have the passed tag.
     *
     * @param resource $stream
     * @param string $tag
     *
     * @return self
     */
    public static function openUniqueNodeStream($stream, string $tag): self
    {
        if (!is_resource($stream)) {
            throw new Exception('Invalid resource passed to openStream');
        }

        $xml = XmlStringStreamer::createUniqueNodeParser($stream, [
            'uniqueNode' => $tag,
        ]);

        return new Reader($xml);
    }

    /**
     * Iterate through the xml document and execute a function for each node.
     * If you set a limit, the reader will stop after that number of nodes read.
     *
     * @param callable $callback
     * @param int $limit
     *
     * @return self
     */
    public function process(callable $callback, int $limit = null): self
    {
        $read = 0;
        while ($node = $this->xml->getNode()) {
            $xmlElement = simplexml_load_string($node);
            $data = XmlToArray::convert($xmlElement->asXml());
            $callback($data);
            $read++;

            if ($limit && $read >= $limit) {
                break;
            }
        }

        return $this;
    }
}
