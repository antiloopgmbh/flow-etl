<?php

declare(strict_types=1);

namespace Flow\ETL;

/**
 * @internal
 */
interface Pipeline
{
    public function add(Loader|Transformer $pipe) : self;

    /**
     * Create clean instance of pipeline, with empty pipes and without source.
     */
    public function cleanCopy() : self;

    /**
     * @return \Generator<int, Rows, mixed, void>
     */
    public function process(Config $config) : \Generator;

    public function source(Extractor $extractor) : self;
}
