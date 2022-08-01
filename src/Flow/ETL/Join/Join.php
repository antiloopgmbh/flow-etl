<?php

declare(strict_types=1);

namespace Flow\ETL\Join;

interface Join
{
    public const inner = 'inner';
    public const left = 'left';
    public const right = 'right';
}
