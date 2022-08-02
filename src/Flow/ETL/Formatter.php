<?php declare(strict_types=1);

namespace Flow\ETL;

interface Formatter
{
    /**
     * @param int|bool $truncate
     */
    public function format(Rows $rows, $truncate = 20) : string;
}
