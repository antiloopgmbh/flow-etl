<?php

declare(strict_types=1);

namespace Flow\ETL\Pipeline;

use Flow\ETL\Config;
use Flow\ETL\DSL\From;
use Flow\ETL\Extractor;
use Flow\ETL\Loader;
use Flow\ETL\Pipeline;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @internal
 */
final class CollectingPipeline implements Pipeline
{
    private Pipeline $nextPipeline;
    private Pipeline $pipeline;

    public function __construct(Pipeline $pipeline)
    {
        $this->nextPipeline = $pipeline->cleanCopy();
        $this->pipeline = $pipeline;
    }

    /**
     * @param Loader|Transformer $pipe
     */
    public function add($pipe) : self
    {
        $this->nextPipeline->add($pipe);

        return $this;
    }

    public function cleanCopy() : Pipeline
    {
        return new self($this->pipeline);
    }

    public function process(Config $config) : \Generator
    {
        $this->nextPipeline->source(From::rows(
            (new Rows())->merge(...\iterator_to_array($this->pipeline->process($config)))
        ));

        return $this->nextPipeline->process($config);
    }

    public function source(Extractor $extractor) : self
    {
        $this->pipeline->source($extractor);

        return $this;
    }
}
