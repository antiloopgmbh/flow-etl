<?php

declare(strict_types=1);

namespace Flow\ETL\DSL;

use Flow\ETL\Cache;
use Flow\ETL\Config;
use Flow\ETL\Extractor;
use Flow\ETL\Extractor\MemoryExtractor;
use Flow\ETL\Extractor\ProcessExtractor;
use Flow\ETL\Memory\ArrayMemory;
use Flow\ETL\Memory\Memory;
use Flow\ETL\Pipeline;
use Flow\ETL\Rows;

/**
 * @infection-ignore-all
 */
class From
{
    /**
     * @param array<array<string, mixed>> $array
     * @param int<1, max> $batch_size
     * @param string $entry_row_name
     */
    final public static function array(array $array, int $batch_size = 100, string $entry_row_name = 'row') : Extractor
    {
        return new MemoryExtractor(new ArrayMemory($array), $batch_size, $entry_row_name);
    }

    final public static function buffer(Extractor $extractor, int $maxRowsSize) : Extractor
    {
        return new Extractor\BufferExtractor($extractor, $maxRowsSize);
    }

    final public static function cache(string $id, Cache $cache, bool $clear = false) : Extractor
    {
        return new Extractor\CacheExtractor($id, $cache, $clear);
    }

    final public static function chain(Extractor ...$extractors) : Extractor
    {
        return new Extractor\ChainExtractor(...$extractors);
    }

    final public static function chunks_from(Extractor $extractor, int $chunkSize) : Extractor
    {
        return new Extractor\ChunkExtractor($extractor, $chunkSize);
    }

    /**
     * @param Memory $memory
     * @param int<1, max> $chunkSize
     * @param string $rowEntryName
     *
     * @return Extractor
     */
    final public static function memory(Memory $memory, int $chunkSize = 100, string $rowEntryName = 'row') : Extractor
    {
        return new MemoryExtractor($memory, $chunkSize, $rowEntryName);
    }

    final public static function pipeline(Pipeline $pipeline, Config $config = null) : Extractor
    {
        return new Extractor\PipelineExtractor($pipeline, $config ?? Config::default());
    }

    final public static function rows(Rows ...$rows) : Extractor
    {
        return new ProcessExtractor(...$rows);
    }
}
