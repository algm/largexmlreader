# Large XML Reader

Read and process HUGE Xml files from any source.

## Description

This package allows to process large XML files with repeating items using PHP 7+.

## Usage

Import the Reader class and use one of the two static constructors.

```php
<?php

use Algm\LargeXmlReader\Xml\Reader;

$xmlStream = fopen($xmlFilePath, 'r');

// open the stream to read all nodes recursively (defaults to two levels)
$reader = Reader::openStream($xmlStream);

// or set the reader to find all repeating <item /> tags
$reader = Reader::openUniqueNodeStream($xmlStream, 'item');
```

In general, the unique node stream performs better than the normal one.

**IMPORTANT LIMITATION**: Unique node reader does not support nested nodes with the same tag.

Once you get the reader instance, use the process method to retrieve a generator for the nodes.

You can use this generator as an iterator.

```php
<?php

$reader = $reader->process();

foreach ($reader as $nodeData) {
    // do something with the node
}
```

The process method accepts a limit param to read a maximum of `$limit` nodes.

