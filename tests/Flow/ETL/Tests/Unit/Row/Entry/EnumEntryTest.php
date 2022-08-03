<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Unit\Row\Entry;

use Flow\ETL\Row\Entry\EnumEntry;
use Flow\ETL\Row\Schema\Definition;
use Flow\ETL\Tests\Fixtures\Enum\BackedIntEnum;
use Flow\ETL\Tests\Fixtures\Enum\BackedStringEnum;
use Flow\ETL\Tests\Fixtures\Enum\BasicEnum;
use PHPUnit\Framework\TestCase;

final class EnumEntryTest extends TestCase
{
    public function test_creating_backed_int_enum_entry() : void
    {
        $enum = new EnumEntry('enum', BackedIntEnum::ONE);

        $this->assertSame(
            BackedIntEnum::ONE,
            $enum->value(),
        );
        $this->assertSame(
            1,
            $enum->value()->value,
        );
    }

    public function test_creating_backed_string_enum_entry() : void
    {
        $enum = new EnumEntry('enum', BackedStringEnum::ONE);

        $this->assertSame(
            BackedStringEnum::ONE,
            $enum->value(),
        );
        $this->assertSame(
            'one',
            $enum->value()->value,
        );
    }

    public function test_creating_basic_enum_entry() : void
    {
        $enum = new EnumEntry('enum', BasicEnum::ONE);

        $this->assertSame(
            BasicEnum::ONE,
            $enum->value(),
        );
        $this->assertSame('enum', $enum->name());
    }

    public function test_definition() : void
    {
        $this->assertEquals(
            Definition::enum(
                'enum',
                BackedStringEnum::class
            ),
            (new EnumEntry('enum', BackedStringEnum::ONE))->definition()
        );
    }

    public function test_is_equal() : void
    {
        $this->assertTrue(
            (new EnumEntry('enum', BasicEnum::ONE))->isEqual(new EnumEntry('enum', BasicEnum::ONE)),
        );
        $this->assertFalse(
            (new EnumEntry('enum', BasicEnum::ONE))->isEqual(new EnumEntry('enum', BackedStringEnum::ONE)),
        );
    }

    public function test_to_string() : void
    {
        $this->assertSame(
            'one',
            (new EnumEntry('enum', BasicEnum::ONE))->toString()
        );
        $this->assertSame(
            'one',
            (new EnumEntry('enum', BackedStringEnum::ONE))->toString()
        );
        $this->assertSame(
            'one',
            (new EnumEntry('enum', BackedIntEnum::ONE))->toString()
        );
    }
}
