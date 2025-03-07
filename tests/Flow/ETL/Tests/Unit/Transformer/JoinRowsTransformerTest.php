<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Unit\Transformer;

use Flow\ETL\DSL\Entry;
use Flow\ETL\Flow;
use Flow\ETL\Join\Condition;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\JoinRowsTransformer;
use PHPUnit\Framework\TestCase;

final class JoinRowsTransformerTest extends TestCase
{
    public function test_inner_join_rows() : void
    {
        $left = new Rows(
            Row::create(Entry::integer('id', 1), Entry::string('country', 'PL')),
            Row::create(Entry::integer('id', 2), Entry::string('country', 'US')),
            Row::create(Entry::integer('id', 3), Entry::string('country', 'FR')),
        );
        $right = (new Flow())->process(
            new Rows(
                Row::create(Entry::string('code', 'PL'), Entry::string('name', 'Poland')),
                Row::create(Entry::string('code', 'US'), Entry::string('name', 'United States')),
                Row::create(Entry::string('code', 'GB'), Entry::string('name', 'Great Britain')),
            )
        );

        $transformer = JoinRowsTransformer::inner($right, Condition::on(['country' => 'code']));

        $this->assertEquals(
            new Rows(
                Row::create(Entry::string('name', 'Poland'), Entry::integer('id', 1), Entry::string('country', 'PL')),
                Row::create(Entry::string('name', 'United States'), Entry::integer('id', 2), Entry::string('country', 'US')),
            ),
            $transformer->transform($left)
        );
    }

    public function test_left_join_rows() : void
    {
        $left = new Rows(
            Row::create(Entry::integer('id', 1), Entry::string('country', 'PL')),
            Row::create(Entry::integer('id', 2), Entry::string('country', 'US')),
            Row::create(Entry::integer('id', 3), Entry::string('country', 'FR')),
        );
        $right = (new Flow())->process(
            new Rows(
                Row::create(Entry::string('code', 'PL'), Entry::string('name', 'Poland')),
                Row::create(Entry::string('code', 'US'), Entry::string('name', 'United States')),
                Row::create(Entry::string('code', 'GB'), Entry::string('name', 'Great Britain')),
            )
        );

        $transformer = JoinRowsTransformer::left($right, Condition::on(['country' => 'code']));

        $this->assertEquals(
            new Rows(
                Row::create(Entry::integer('id', 1), Entry::string('country', 'PL'), Entry::string('name', 'Poland')),
                Row::create(Entry::integer('id', 2), Entry::string('country', 'US'), Entry::string('name', 'United States')),
                Row::create(Entry::integer('id', 3), Entry::string('country', 'FR'), Entry::null('name')),
            ),
            $transformer->transform($left)
        );
    }

    public function test_right_join_rows() : void
    {
        $left = new Rows(
            Row::create(Entry::integer('id', 1), Entry::string('country', 'PL')),
            Row::create(Entry::integer('id', 2), Entry::string('country', 'US')),
            Row::create(Entry::integer('id', 3), Entry::string('country', 'FR')),
        );
        $right = (new Flow())->process(
            new Rows(
                Row::create(Entry::string('code', 'PL'), Entry::string('name', 'Poland')),
                Row::create(Entry::string('code', 'US'), Entry::string('name', 'United States')),
                Row::create(Entry::string('code', 'GB'), Entry::string('name', 'Great Britain')),
            )
        );

        $transformer = JoinRowsTransformer::right($right, Condition::on(['country' => 'code']));

        $this->assertEquals(
            new Rows(
                Row::create(Entry::string('name', 'Poland'), Entry::string('code', 'PL'), Entry::integer('id', 1)),
                Row::create(Entry::string('name', 'United States'), Entry::string('code', 'US'), Entry::integer('id', 2)),
                Row::create(Entry::string('name', 'Great Britain'), Entry::string('code', 'GB'), Entry::null('id')),
            ),
            $transformer->transform($left)
        );
    }
}
