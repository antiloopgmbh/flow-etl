<?php

declare(strict_types=1);

namespace Flow\ETL\Pipeline;

use Flow\ETL\Config;
use Flow\ETL\Extractor;
use Flow\ETL\Loader;
use Flow\ETL\Pipeline;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

final class VoidPipeline implements Pipeline
{
    private Pipeline $pipeline;

    public function __construct(Pipeline $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @param Loader|Transformer $pipe
     */
    public function add($pipe) : self
    {
        return $this;
    }

    public function cleanCopy() : Pipeline
    {
        return new self($this->pipeline->cleanCopy());
    }

    /**
     * @psalm-suppress UnusedForeachValue
     */
    public function process(Config $config) : \Generator
    {
        foreach ($this->pipeline->process($config) as $rows) {
            // do nothing, put those rows into void
        }

        yield new Rows();
    }

    public function source(Extractor $extractor) : self
    {
        return $this;
    }
}
