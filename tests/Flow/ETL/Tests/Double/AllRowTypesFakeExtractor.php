<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Double;

use Flow\ETL\DSL\Entry;
use Flow\ETL\Extractor;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Tests\Fixtures\Enum\BackedStringEnum;

final class AllRowTypesFakeExtractor implements Extractor
{
    public function __construct(private readonly int $total, private readonly int $rowsSize)
    {
    }

    /**
     * @return \Generator<int, Rows, mixed, void>
     */
    public function extract() : \Generator
    {
        for ($i = 0; $i < $this->total; $i++) {
            $id = $this->rowsSize * $i;

            $rows = [];

            for ($r = 0; $r < $this->rowsSize; $r++) {
                $rows[] = Row::create(
                    Entry::integer('id', $id + $r),
                    Entry::float('price', \random_int(100, 100000) / 100),
                    Entry::boolean('deleted', false),
                    Entry::datetime('created-at', new \DateTimeImmutable('now')),
                    Entry::null('phase'),
                    Entry::integer('status', 0),
                    Entry::array(
                        'array',
                        [
                            ['id' => 1, 'status' => 'NEW'],
                            ['id' => 2, 'status' => 'PENDING'],
                        ]
                    ),
                    Entry::structure(
                        'items',
                        Entry::integer('item-id', 1),
                        Entry::string('name', 'one'),
                    ),
                    Entry::collection(
                        'tags',
                        new Row\Entries(Entry::integer('item-id', 1), Entry::string('name', 'one')),
                        new Row\Entries(Entry::integer('item-id', 2), Entry::string('name', 'two')),
                        new Row\Entries(Entry::integer('item-id', 3), Entry::string('name', 'three'))
                    ),
                    Entry::object('object', new \ArrayIterator([1, 2, 3])),
                    Entry::enum('enum', BackedStringEnum::three)
                );
            }

            \shuffle($rows);

            yield new Rows(...$rows);
        }
    }
}
