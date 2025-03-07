<?php

declare(strict_types=1);

namespace Flow\ETL\Pipeline;

use Flow\ETL\Async\Socket\Server\Server;
use Flow\ETL\Async\Socket\Server\ServerProtocol;
use Flow\ETL\Async\Socket\Worker\Pool;
use Flow\ETL\Async\Socket\Worker\WorkerLauncher;
use Flow\ETL\Config;
use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Extractor;
use Flow\ETL\Extractor\ProcessExtractor;
use Flow\ETL\Loader;
use Flow\ETL\Pipeline;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

final class LocalSocketPipeline implements Pipeline
{
    private Extractor $extractor;

    private readonly Pipes $pipes;

    private readonly int $totalWorkers;

    public function __construct(
        private readonly Server $server,
        private readonly WorkerLauncher $launcher,
        int $workers
    ) {
        if ($workers < 1) {
            throw new InvalidArgumentException("Number of workers can't be lower than 1, given: {$workers}");
        }

        $this->totalWorkers = $workers;
        $this->pipes = Pipes::empty();
        $this->extractor = new ProcessExtractor(new Rows());
    }

    public function add(Loader|Transformer $pipe) : self
    {
        $this->pipes->add($pipe);

        return $this;
    }

    public function cleanCopy() : Pipeline
    {
        return new Pipeline\SynchronousPipeline();
    }

    public function process(Config $config) : \Generator
    {
        $pool = Pool::generate($this->totalWorkers);

        $id = \uniqid('flow_async_pipeline', true);

        $this->server->initialize(new ServerProtocol($config, $id, $pool, $this->extractor, $this->pipes));

        $this->launcher->launch($pool, $this->server->host());

        $this->server->start();

        return $config->cache()->read($id);
    }

    public function source(Extractor $extractor) : self
    {
        $this->extractor = $extractor;

        return $this;
    }
}
