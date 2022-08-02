<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast\EntryCaster;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row\Entry;
use Flow\ETL\Row\Entry\TypedCollection\ObjectType;
use Flow\ETL\Row\Entry\TypedCollection\ScalarType;
use Flow\ETL\Row\Entry\TypedCollection\Type;
use Flow\ETL\Row\EntryConverter;
use Flow\ETL\Row\ValueConverter;
use Flow\ETL\Transformer\Cast\ValueCaster\AnyToBooleanCaster;
use Flow\ETL\Transformer\Cast\ValueCaster\AnyToFloatCaster;
use Flow\ETL\Transformer\Cast\ValueCaster\AnyToIntegerCaster;
use Flow\ETL\Transformer\Cast\ValueCaster\AnyToStringCaster;
use Flow\ETL\Transformer\Cast\ValueCaster\StringToDateTimeCaster;

/**
 * @implements EntryConverter<array{type: Type}>
 * @psalm-immutable
 */
final class AnyToListCaster implements EntryConverter
{
    private Type $type;
    private ?ValueConverter $valueConverter;

    public function __construct(Type $type, ?ValueConverter $valueConverter = null)
    {
        $this->type = $type;
        $this->valueConverter = $valueConverter;
    }

    public function __serialize() : array
    {
        return ['type' => $this->type];
    }

    public function __unserialize(array $data) : void
    {
        $this->type = $data['type'];
    }

    public function convert(Entry $entry) : Entry
    {
        /**
         * @psalm-suppress ImpureFunctionCall
         */
        return new Entry\ListEntry(
            $entry->name(),
            $this->type,
            \array_map(
                function ($value) {
                    if ($this->valueConverter !== null) {
                        return $this->valueConverter->convert($value);
                    }

                    if ($this->type instanceof ObjectType) {
                        if (\is_a($this->type->getClass(), \DateTimeInterface::class, true) && \is_string($value)) {
                            return (new StringToDateTimeCaster())->convert($value);
                        }

                        throw new RuntimeException('Value ' . \gettype($value) . " can't be automatically cast {$this->type->toString()}, please provide custom ValueConverter.");
                    }

                    /** @var ScalarType $type */
                    $type = $this->type;

                    switch ($type->getValue()) {
                        case ScalarType::INTEGER:
                            return (new AnyToIntegerCaster())->convert($value);
                        case ScalarType::STRING:
                            return (new AnyToStringCaster())->convert($value);
                        case ScalarType::BOOLEAN:
                            return (new AnyToBooleanCaster())->convert($value);
                        case ScalarType::FLOAT:
                            return (new AnyToFloatCaster())->convert($value);
                        default:
                            throw new InvalidArgumentException("Unsupported scalar type: {$type}");
                    }
                },
                (array) $entry->value()
            )
        );
    }
}
