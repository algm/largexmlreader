<?php

namespace Algm\LargeXmlReader\Xml;

use Exception;
use Generator;
use Prewk\XmlStringStreamer;

class Reader
{
    /**
     * XmlReader Instance
     *
     * @var XmlStringStreamer
     */
    protected $xml;

    /**
     * Constructor
     *
     * @param XmlStringStreamer $xml
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
     * @param array $options
     * @return self
     * @throws Exception
     */
    public static function openStream($stream, int $depth = 2, array $options = []): self
    {
        if (!is_resource($stream)) {
            throw new Exception('Invalid resource passed to openStream');
        }

        $parserOptions = array_merge(static::getOptions($options), [
            'captureDepth' => $depth,
        ]);

        $xml = XmlStringStreamer::createStringWalkerParser($stream, $parserOptions);

        return new Reader($xml);
    }

    /**
     * Returns an instance of the reader from the passed stream that
     * iterates through the nodes that have the passed tag.
     *
     * @param resource $stream
     * @param string $tag
     * @param array $options
     * @return self
     * @throws Exception
     */
    public static function openUniqueNodeStream($stream, string $tag, array $options = []): self
    {
        if (!is_resource($stream)) {
            throw new Exception('Invalid resource passed to openStream');
        }

        $parserOptions = array_merge(static::getOptions($options), [
            'uniqueNode' => $tag,
        ]);

        $xml = XmlStringStreamer::createUniqueNodeParser($stream, $parserOptions);

        return new Reader($xml);
    }

    /**
     * Iterate through the xml document and return an array for each node.
     * If you set a limit, the reader will stop after that number of nodes read.
     *
     * Warning: Recursive tags are not supported!
     *
     * @param int $limit
     *
     * @return Generator
     */
    public function process(int $limit = null): Generator
    {
        $read = 0;
        while ($node = $this->xml->getNode()) {
            $xmlElement = simplexml_load_string($node, null, LIBXML_NOCDATA);
            yield json_decode(json_encode((array) $xmlElement), 1);
            $read++;

            if ($limit && $read >= $limit) {
                break;
            }
        }

        return yield from [];
    }

    protected static function getOptions($options = []): array
    {
        $defaultOptions = [
            'expectGT' => true,
        ];

        return array_merge($defaultOptions, $options);
    }
}
