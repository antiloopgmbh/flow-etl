<?php

declare(strict_types=1);

namespace Flow\ETL\Async\Socket\Worker\Pool;

abstract class WorkerStatus
{
    const CONNECTED = 'connected';
    const DISCONNECTED = 'disconnected';
    const NEW = 'new';
}
