<?php

declare(strict_types=1);

namespace Flow\ETL\Async\Socket\Worker\Pool;

final class Worker
{
    private string $status;
    private string $id;

    public function __construct(string $id)
    {
        $this->status = WorkerStatus::NEW;
        $this->id = $id;
    }

    public function connect() : void
    {
        $this->status = WorkerStatus::CONNECTED;
    }

    public function disconnect() : void
    {
        $this->status = WorkerStatus::DISCONNECTED;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function status() : string
    {
        return $this->status;
    }
}
