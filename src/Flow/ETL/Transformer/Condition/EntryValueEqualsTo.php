<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Condition;

use Flow\ETL\Row;

final class EntryValueEqualsTo implements RowCondition
{
    private string $entryName;
    private $value;
    private bool $identical;

    /**
     * @param mixed $value
     */
    public function __construct(
        string $entryName,
        $value,
        bool $identical = true
    ) {
        $this->entryName = $entryName;
        $this->value = $value;
        $this->identical = $identical;
    }

    public function isMetFor(Row $row) : bool
    {
        if (!$row->entries()->has($this->entryName)) {
            return false;
        }

        return $this->identical
            ? $row->valueOf($this->entryName) === $this->value
            : $row->valueOf($this->entryName) == $this->value;
    }
}
