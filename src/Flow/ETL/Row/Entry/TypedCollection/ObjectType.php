<?php

declare(strict_types=1);

namespace Flow\ETL\Row\Entry\TypedCollection;

use Flow\ETL\Exception\InvalidArgumentException;
use Symfony\PolyFill\Php81\Php81;

/**
 * @psalm-immutable
 */
final class ObjectType implements Type
{
    /**
     * @var string
     */
    private $class;

    public function __construct(string $class)
    {
        $this->class = $class;

        /** @psalm-suppress ImpureFunctionCall */
        if (!\class_exists($class) && !\interface_exists($this->class)) {
            throw new InvalidArgumentException("Class {$class} not found");
        }
    }

    /**
     * @param string $class
     */
    public static function of(string $class) : self
    {
        return new self($class);
    }

    public function isEqual(Type $type) : bool
    {
        return $type instanceof self && $type->class === $this->class;
    }

    public function isValid(array $collection) : bool
    {
        if (!Php81::array_is_list($collection)) {
            return false;
        }

        foreach ($collection as $value) {
            if (!\is_object($value)) {
                return false;
            }

            if (!$value instanceof $this->class) {
                return false;
            }
        }

        return true;
    }

    public function toString() : string
    {
        return 'object<' . $this->class . '>';
    }
}
