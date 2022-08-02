<?php

declare(strict_types=1);

namespace Flow\ETL\Exception;

use Flow\ETL\Row\Schema;
use Flow\ETL\Row\Schema\Formatter\ASCIISchemaFormatter;
use Flow\ETL\Rows;

final class SchemaValidationException extends RuntimeException
{
    private Schema $schema;
    private Rows $rows;

    public function __construct(Schema $schema, Rows $rows)
    {
        $this->schema = $schema;
        $this->rows = $rows;

        $schema = (new ASCIISchemaFormatter())->format($this->schema);
        $rowsSchema = (new ASCIISchemaFormatter())->format($rows->schema());

        parent::__construct(
            <<<SCHEMA
Given schema:
{$schema}
Does not match rows: 
{$rowsSchema}
SCHEMA
        );
    }

    public function rows() : Rows
    {
        return $this->rows;
    }

    public function schema() : Schema
    {
        return $this->schema;
    }
}
