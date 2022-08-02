<?php

declare(strict_types=1);

namespace Flow\ETL\Pipeline;

use Flow\ETL\Config;
use Flow\ETL\DSL\From;
use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Extractor;
use Flow\ETL\Loader;
use Flow\ETL\Pipeline;
use Flow\ETL\Transformer;

/**
 * @internal
 */
final class ParallelizingPipeline implements Pipeline
{
    private Pipeline $nextPipeline;
    private Pipeline $pipeline;
    private int $parallel;

    public function __construct(
        Pipeline $pipeline,
        int $parallel
    ) {
        if ($parallel < 1) {
            throw new InvalidArgumentException("Parallel value can't be lower than 1.");
        }

        $this->nextPipeline = $pipeline->cleanCopy();
        $this->pipeline = $pipeline;
        $this->parallel = $parallel;
    }

    public function add(Loader|Transformer $pipe) : self
    {
        $this->nextPipeline->add($pipe);

        return $this;
    }

    public function cleanCopy() : Pipeline
    {
        return new self($this->pipeline, $this->parallel);
    }

    public function process(Config $config) : \Generator
    {
        $this->nextPipeline->source(
            From::chunks_from(
                From::pipeline($this->pipeline, $config),
                $this->parallel
            )
        );

        return $this->nextPipeline->process($config);
    }

    public function source(Extractor $extractor) : self
    {
        $this->pipeline->source($extractor);

        return $this;
    }
}
