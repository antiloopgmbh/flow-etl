<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Unit\Row\Schema\Constraint;

use Flow\ETL\DSL\Entry;
use Flow\ETL\Row\Entry\TypedCollection\ScalarType;
use Flow\ETL\Row\Schema\Constraint\CollectionType;
use PHPUnit\Framework\TestCase;

final class CollectionTypeTest extends TestCase
{
    public function test_against_invalid_typed_collection() : void
    {
        $this->assertFalse((new CollectionType(new ScalarType(ScalarType::INTEGER)))->isSatisfiedBy(Entry::list_of_string('id', ['one', 'two'])));
    }

    public function test_against_not_typed_collection() : void
    {
        $this->assertFalse((new CollectionType(new ScalarType(ScalarType::STRING)))->isSatisfiedBy(Entry::integer('id', 1)));
    }

    public function test_against_valid_typed_collection() : void
    {
        $this->assertTrue((new CollectionType(new ScalarType(ScalarType::STRING)))->isSatisfiedBy(Entry::list_of_string('id', ['one', 'two'])));
    }
}
