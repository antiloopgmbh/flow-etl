<?php

declare(strict_types=1);

namespace Flow\ETL\Loader\StreamLoader;

abstract class Output
{
    const ROWS = 'rows';
    const ROWS_AND_SCHEMA = 'rows_and_schema';
    const SCHEMA = 'schema';
}
