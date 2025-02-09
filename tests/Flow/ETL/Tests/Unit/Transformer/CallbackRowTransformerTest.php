<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Unit\Transformer;

use Flow\ETL\DSL\Transform;
use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\Serializer\NativePHPSerializer;
use PHPUnit\Framework\TestCase;

class CallbackRowTransformerTest extends TestCase
{
    public function test_replacing_dashes_in_entry_name_with_str_replace_callback() : void
    {
        $callbackTransformer = Transform::callback_row(
            fn (Row $row) : Row => $row->remove('old-int')
        );

        $rows = $callbackTransformer->transform(
            new Rows(
                Row::create(
                    new Row\Entry\IntegerEntry('old-int', 1000),
                    new Entry\StringEntry('string-entry ', 'String entry')
                )
            )
        );

        $this->assertEquals(new Rows(
            Row::create(
                new Entry\StringEntry('string-entry ', 'String entry')
            )
        ), $rows);
    }

    public function test_replacing_dashes_in_entry_name_with_str_replace_callback_with_serialization() : void
    {
        $callbackTransformer = Transform::callback_row(
            fn (Row $row) : Row => $row->remove('old-int')
        );

        $serialization = new NativePHPSerializer();

        $rows = $serialization->unserialize($serialization->serialize($callbackTransformer))->transform(
            new Rows(
                Row::create(
                    new Row\Entry\IntegerEntry('old-int', 1000),
                    new Entry\StringEntry('string-entry ', 'String entry')
                )
            )
        );

        $this->assertEquals(new Rows(
            Row::create(
                new Entry\StringEntry('string-entry ', 'String entry')
            )
        ), $rows);
    }
}
