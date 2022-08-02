<?php

declare(strict_types=1);

namespace Flow\ETL\Pipeline;

use Flow\ETL\Config;
use Flow\ETL\Extractor;
use Flow\ETL\Loader;
use Flow\ETL\Pipeline;
use Flow\ETL\Transformer;

final class NestedPipeline implements Pipeline
{
    private Pipeline $currentPipeline;
    private Pipeline $nextPipeline;

    public function __construct(
        Pipeline $currentPipeline,
        Pipeline $nextPipeline
    ) {
        $this->currentPipeline = $currentPipeline;
        $this->nextPipeline = $nextPipeline;
    }

    /**
     * @param Loader|Transformer $pipe
     */
    public function add($pipe) : Pipeline
    {
        $this->nextPipeline->add($pipe);

        return $this;
    }

    public function cleanCopy() : Pipeline
    {
        return new self(
            $this->currentPipeline->cleanCopy(),
            $this->nextPipeline->cleanCopy(),
        );
    }

    public function process(Config $config) : \Generator
    {
        foreach ($this->nextPipeline->source(new Extractor\PipelineExtractor($this->currentPipeline, $config))->process($config) as $rows) {
            yield $rows;
        }
    }

    public function source(Extractor $extractor) : Pipeline
    {
        $this->currentPipeline->source($extractor);

        return $this;
    }
}
