<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\DataFrameFactory;
use Flow\ETL\Join\Condition;
use Flow\ETL\Join\Join;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @implements Transformer<array{factory: DataFrameFactory, condition: Condition, type: Join}>
 * @psalm-immutable
 */
final class JoinEachRowsTransformer implements Transformer
{
    private DataFrameFactory $factory;
    private Condition $condition;
    private Join $type;

    private function __construct(
        DataFrameFactory $factory,
        Condition $condition,
        Join $type
    ) {
        $this->factory = $factory;
        $this->condition = $condition;
        $this->type = $type;
    }

    /**
     * @psalm-pure
     */
    public static function inner(DataFrameFactory $right, Condition $condition) : self
    {
        return new self($right, $condition, Join::inner);
    }

    /**
     * @psalm-pure
     */
    public static function left(DataFrameFactory $right, Condition $condition) : self
    {
        return new self($right, $condition, Join::left);
    }

    /**
     * @psalm-pure
     */
    public static function right(DataFrameFactory $right, Condition $condition) : self
    {
        return new self($right, $condition, Join::right);
    }

    public function __serialize() : array
    {
        return [
            'factory' => $this->factory,
            'condition' => $this->condition,
            'type' => $this->type,
        ];
    }

    public function __unserialize(array $data) : void
    {
        $this->factory = $data['factory'];
        $this->condition = $data['condition'];
        $this->type = $data['type'];
    }

    /**
     * @psalm-suppress ImpureMethodCall
     *
     * @throws \Flow\ETL\Exception\InvalidArgumentException
     */
    public function transform(Rows $rows) : Rows
    {
         switch ($this->type) {
             case Join::left:
                 return $rows->joinLeft($this->factory->from($rows)->fetch(), $this->condition);
             case Join::right:
                 return $rows->joinRight($this->factory->from($rows)->fetch(), $this->condition);
             default:
                 return $rows->joinInner($this->factory->from($rows)->fetch(), $this->condition);
         }
    }
}
