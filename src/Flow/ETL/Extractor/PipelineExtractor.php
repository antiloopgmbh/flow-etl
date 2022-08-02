<?php

declare(strict_types=1);

namespace Flow\ETL\Extractor;

use Flow\ETL\Config;
use Flow\ETL\Extractor;
use Flow\ETL\Pipeline;
use Flow\ETL\Rows;

/**
 * @psalm-immutable
 */
final class PipelineExtractor implements Extractor
{
    private Pipeline $pipeline;
    private Config $config;

    public function __construct(
        Pipeline $pipeline,
        Config $config
    ) {
        $this->pipeline = $pipeline;
        $this->config = $config;
    }

    /**
     * @return \Generator<int, Rows, mixed, void>
     */
    public function extract() : \Generator
    {
        /** @psalm-suppress ImpureMethodCall */
        return $this->pipeline->process($this->config);
    }
}
