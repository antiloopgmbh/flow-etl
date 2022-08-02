<?php

declare(strict_types=1);

namespace Flow\ETL\DSL;

use Flow\ETL\Formatter;
use Flow\ETL\Loader;
use Flow\ETL\Loader\StreamLoader\Output;
use Flow\ETL\Memory\Memory;
use Flow\ETL\Row\Schema\Formatter\ASCIISchemaFormatter;
use Flow\ETL\Row\Schema\SchemaFormatter;
use Flow\ETL\Transformer;

/**
 * @infection-ignore-all
 */
class To
{
    final public static function buffer(Loader $overflowLoader, int $bufferSize) : Loader
    {
        return new Loader\BufferLoader($overflowLoader, $bufferSize);
    }

    final public static function callback(callable $callable) : Loader
    {
        return new Loader\CallbackLoader($callable);
    }

    final public static function memory(Memory $memory) : Loader
    {
        return new Loader\MemoryLoader($memory);
    }

    /**
     * @param int|bool $truncate
     */
    final public static function output($truncate = 20, string $output = Output::ROWS, Formatter $formatter = null, SchemaFormatter $schemaFormatter = null) : Loader
    {
        return Loader\StreamLoader::output($truncate, $output, $formatter ?? new Formatter\AsciiTableFormatter(), $schemaFormatter ?? new ASCIISchemaFormatter());
    }

    /**
     * @param int|bool $truncate
     */
    final public static function stderr($truncate = 20, string $output = Output::ROWS, Formatter $formatter = null, SchemaFormatter $schemaFormatter = null) : Loader
    {
        return Loader\StreamLoader::stderr($truncate, $output, $formatter ?? new Formatter\AsciiTableFormatter(), $schemaFormatter ?? new ASCIISchemaFormatter());
    }

    /**
     * @param int|bool $truncate
     */
    final public static function stdout($truncate = 20, string $output = Output::ROWS, Formatter $formatter = null, SchemaFormatter $schemaFormatter = null) : Loader
    {
        return Loader\StreamLoader::stdout($truncate, $output, $formatter ?? new Formatter\AsciiTableFormatter(), $schemaFormatter ?? new ASCIISchemaFormatter());
    }

    /**
     * @param int|bool $truncate
     */
    final public static function stream(string $uri, $truncate = 20, string $output = Output::ROWS, string $mode = 'w', Formatter $formatter = null, SchemaFormatter $schemaFormatter = null) : Loader
    {
        return new Loader\StreamLoader($uri, $mode, $truncate, $output, $formatter ?? new Formatter\AsciiTableFormatter(), $schemaFormatter ?? new ASCIISchemaFormatter());
    }

    final public static function transform_to(Transformer $transformer, Loader $loader) : Loader
    {
        return new Loader\TransformerLoader($transformer, $loader);
    }
}
