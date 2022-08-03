<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Unit\Transformer;

use Flow\ETL\DSL\Transform;
use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\Math\Operation;
use PHPUnit\Framework\TestCase;

final class MathValueOperationTransformerTest extends TestCase
{
    public function math_operations_provider() : \Generator
    {
        yield [new Entry\IntegerEntry('left', 10), 10, Operation::ADD, 20, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 10), 10, Operation::SUBTRACT, 0, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 10), 5, Operation::DIVIDE, 2, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 10), 5, Operation::MULTIPLY, 50, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 2), 3, Operation::POWER, 8, Entry\IntegerEntry::class];
        yield [new Entry\IntegerEntry('left', 5), 2, Operation::DIVIDE, 2.5, Entry\FloatEntry::class];
        yield [new Entry\IntegerEntry('left', 5), 2, Operation::MODULO, 1, Entry\IntegerEntry::class];
    }

    /**
     * @dataProvider math_operations_provider
     *
     * @param int|float $rightValue
     * @param int|float $result
     */
    public function test_math_operations(Entry $leftEntry, $rightValue, string $operation, $result, string $resultClass) : void
    {
        switch ($operation) {
            case Operation::ADD:
                $rows = Transform::add_value($leftEntry->name(), $rightValue)->transform(new Rows(Row::create($leftEntry)));
                break;
            case Operation::SUBTRACT:
                $rows = Transform::subtract_value($leftEntry->name(), $rightValue)->transform(new Rows(Row::create($leftEntry)));
                break;
            case Operation::DIVIDE:
                $rows = Transform::divide_by($leftEntry->name(), $rightValue)->transform(new Rows(Row::create($leftEntry)));
                break;
            case Operation::MULTIPLY:
                $rows = Transform::multiply_by($leftEntry->name(), $rightValue)->transform(new Rows(Row::create($leftEntry)));
                break;
            case Operation::MODULO:
                $rows = Transform::modulo_by($leftEntry->name(), $rightValue)->transform(new Rows(Row::create($leftEntry)));
                break;
            case Operation::POWER:
                $rows = Transform::power_of($leftEntry->name(), $rightValue)->transform(new Rows(Row::create($leftEntry)));
                break;
        };

        $this->assertSame(
            [
                [
                    'left' => $result,
                ],
            ],
            $rows->toArray()
        );
        $this->assertInstanceOf(
            $resultClass,
            $rows->first()->get($leftEntry->name())
        );
    }
}
