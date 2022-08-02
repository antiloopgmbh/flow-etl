<?php

declare(strict_types=1);

namespace Flow\ETL\Stream;

abstract class Mode: string
{
    const READ = 'r';

    const READ_BINARY = 'rb';

    const READ_WRITE = 'r+';

    const WRITE = 'w';

    const WRITE_BINARY = 'wb';
}
