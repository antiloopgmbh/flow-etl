<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Math;

abstract class Operation: string
{
    const ADD = 'add';
    const DIVIDE = 'divide';
    const MODULO = 'modulo';
    const MULTIPLY = 'multiply';
    const POWER = 'power';
    const SUBTRACT = 'subtract';
}
