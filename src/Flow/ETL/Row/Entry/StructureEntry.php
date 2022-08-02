<?php

declare(strict_types=1);

namespace Flow\ETL\Row\Entry;

use Flow\ArrayComparison\ArrayComparison;
use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Row\Entry;
use Flow\ETL\Row\Schema\Definition;

/**
 * @implements Entry<array<Entry>, array{name: string, entries: array<Entry>}>
 * @psalm-immutable
 */
final class StructureEntry implements \Stringable, Entry
{
    /**
     * @var array<Entry>
     */
    private array $entries;
    private string $name;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, Entry ...$entries)
    {
        if (!\strlen($name)) {
            throw InvalidArgumentException::because('Entry name cannot be empty');
        }

        $this->entries = $entries;
        $this->name = $name;
    }

    public function __serialize() : array
    {
        return [
            'name' => $this->name,
            'entries' => $this->entries,
        ];
    }

    public function __toString() : string
    {
        return $this->toString();
    }

    public function __unserialize(array $data) : void
    {
        $this->name = $data['name'];
        $this->entries = $data['entries'];
    }

    public function definition() : Definition
    {
        return Definition::structure($this->name, false);
    }

    public function is(string $name) : bool
    {
        return $this->name === $name;
    }

    public function isEqual(Entry $entry) : bool
    {
        return $this->is($entry->name()) && $entry instanceof self && (new ArrayComparison())->equals($this->value(), $entry->value());
    }

    public function map(callable $mapper) : Entry
    {
        return new self($this->name, ...$mapper($this->entries));
    }

    public function name() : string
    {
        return $this->name;
    }

    public function rename(string $name) : Entry
    {
        return new self($name, ...$this->entries);
    }

    public function toString() : string
    {
        $array = [];

        foreach ($this->entries as $entry) {
            $array[$entry->name()] = $entry->toString();
        }

        return \json_encode($array, JSON_THROW_ON_ERROR);
    }

    public function value() : array
    {
        return \array_values($this->entries);
    }
}
