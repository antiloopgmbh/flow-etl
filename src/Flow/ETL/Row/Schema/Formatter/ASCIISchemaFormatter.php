<?php

declare(strict_types=1);

namespace Flow\ETL\Row\Schema\Formatter;

use Flow\ETL\Row\Schema;
use Flow\ETL\Row\Schema\SchemaFormatter;

final class ASCIISchemaFormatter implements SchemaFormatter
{
    public function format(Schema $schema) : string
    {
        /** @var array<string, string> $entries */
        $entries = [];

        foreach ($schema->definitions() as $definition) {
            switch (\count($definition->types())) {
                case 1:
                    $type = $definition->types()[0];
                    break;
                default:
                    $type = '[' . \implode(', ', $definition->types()) . ']';
                    break;
            }

            $entries[$definition->entry()] = '|-- ' . $definition->entry() . ': ' . $type . ' (nullable = ' . ($definition->isNullable() ? 'true' : 'false') . ')';
        }

        \ksort($entries);

        $output = "schema\n";
        $output .= \implode("\n", $entries);

        return $output . "\n";
    }
}
