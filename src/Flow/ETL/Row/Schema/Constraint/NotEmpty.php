<?php

declare(strict_types=1);

namespace Flow\ETL\Row\Schema\Constraint;

use Flow\ETL\Row\Entry;
use Flow\ETL\Row\Schema\Constraint;

/**
 * @implements Constraint<array<mixed>>
 */
final class NotEmpty implements Constraint
{
    public function __construct()
    {
    }

    public function __serialize() : array
    {
        return [];
    }

    public function __unserialize(array $data) : void
    {
    }

    public function isSatisfiedBy(Entry $entry) : bool
    {
        /** @psalm-suppress MixedArgument */
        switch (\get_class($entry)) {
            case Entry\ArrayEntry::class:
            case Entry\CollectionEntry::class:
            case Entry\StructureEntry::class:
            /** @phpstan-ignore-next-line  */
            case Entry\ListEntry::class:
                return (bool) \count($entry->value());
            case Entry\StringEntry::class:
                return $entry->value() !== '';
            case Entry\JsonEntry::class:
                return !\in_array($entry->value(), ['', '[]', '{}'], true);
            default:
                return true; //everything else can't be empty
        }
    }
}
