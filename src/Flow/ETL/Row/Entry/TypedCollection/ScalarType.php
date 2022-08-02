<?php

declare(strict_types=1);

namespace Flow\ETL\Row\Entry\TypedCollection;

use Flow\ETL\Exception\InvalidArgumentException;
use Symfony\PolyFill\Php81\Php81;

/**
 * @psalm-immutable
 */
class ScalarType implements Type
{
    const BOOLEAN = 'boolean';
    const FLOAT = 'float';
    const INTEGER = 'integer';
    const STRING = 'string';

    private string $value;

    public function __construct(string $type)
    {
        $this->value = $type;
    }

    public static function fromString(string $value) : ScalarType
    {
        switch (\strtolower($value)) {
            case 'integer':
                $type = self::INTEGER;
                break;
            case 'float':
            case 'double':
                $type = self::FLOAT;
                break;
            case 'string':
                $type = self::STRING;
                break;
            case 'boolean':
                $type = self::BOOLEAN;
                break;
            default:
                throw new InvalidArgumentException("Unsupported scalar type: {$value}");
        }

        return new self($type);
    }

    public function isEqual(Type $type) : bool
    {
        return $type instanceof self && $type->value === $this->value;
    }

    public function isValid(array $collection) : bool
    {
        if (!Php81::array_is_list($collection)) {
            return false;
        }

        foreach ($collection as $value) {
            if (!\is_scalar($value)) {
                return false;
            }

            if ($this->value === 'float') {
                // php gettype returns double for floats for historical reasons
                if ('double' !== \gettype($value)) {
                    return false;
                }
            } else {
                if ($this->value !== \gettype($value)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function toString() : string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
