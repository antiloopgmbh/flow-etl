<?php

declare(strict_types=1);

namespace Flow\ETL\Loader;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Formatter;
use Flow\ETL\Loader;
use Flow\ETL\Loader\StreamLoader\Output;
use Flow\ETL\Row\Schema\Formatter\ASCIISchemaFormatter;
use Flow\ETL\Row\Schema\SchemaFormatter;
use Flow\ETL\Rows;
use Flow\ETL\Stream\Mode;

/**
 * @implements Loader<array{url: string, mode: Mode, truncate: int|bool, output: Output, formatter: Formatter, schema_formatter: SchemaFormatter}>
 */
final class StreamLoader implements Loader
{
    private string $url;
    private Mode $mode;
    private $truncate;
    private Output $output;
    private Formatter $formatter;
    private SchemaFormatter $schemaFormatter;

    /**
     * @param string $url all protocols supported by PHP are allowed https://www.php.net/manual/en/wrappers.php
     * @param Mode $mode only writing modes explained in https://www.php.net/manual/en/function.fopen.php are supported
     * @param bool|int $truncate if false or 0, then columns in display are not truncated
     * @param Formatter $formatter - if not passed AsciiTableFormatter is used
     */
    public function __construct(
        string $url,
        Mode $mode = Mode::WRITE,
        $truncate = 20,
        Output $output = Output::rows,
        Formatter $formatter = null,
        SchemaFormatter $schemaFormatter = null
    ) {
        $this->url = $url;
        $this->mode = $mode;
        $this->truncate = $truncate;
        $this->output = $output;
        $this->formatter = $formatter ?? new Formatter\AsciiTableFormatter();
        $this->schemaFormatter = $schemaFormatter ?? new ASCIISchemaFormatter();
    }

    /**
     * @param int|bool $truncate
     */
    public static function output($truncate = 20, Output $output = Output::rows, Formatter $formatter = new Formatter\AsciiTableFormatter(), SchemaFormatter $schemaFormatter = new ASCIISchemaFormatter()) : self
    {
        return new self('php://output', Mode::WRITE, $truncate, $output, $formatter, $schemaFormatter);
    }

    public static function stderr($truncate = 20, Output $output = Output::rows, Formatter $formatter = new Formatter\AsciiTableFormatter(), SchemaFormatter $schemaFormatter = new ASCIISchemaFormatter()) : self
    {
        return new self('php://stderr', Mode::WRITE, $truncate, $output, $formatter, $schemaFormatter);
    }

    public static function stdout($truncate = 20, Output $output = Output::rows, Formatter $formatter = new Formatter\AsciiTableFormatter(), SchemaFormatter $schemaFormatter = new ASCIISchemaFormatter()) : self
    {
        return new self('php://stdout', Mode::WRITE, $truncate, $output, $formatter, $schemaFormatter);
    }

    public function __serialize() : array
    {
        return [
            'url' => $this->url,
            'mode' => $this->mode,
            'truncate' => $this->truncate,
            'output' => $this->output,
            'formatter' => $this->formatter,
            'schema_formatter' => $this->schemaFormatter,
        ];
    }

    public function __unserialize(array $data) : void
    {
        $this->url = $data['url'];
        $this->mode = $data['mode'];
        $this->truncate = $data['truncate'];
        $this->output = $data['output'];
        $this->formatter = $data['formatter'];
        $this->schemaFormatter = $data['schema_formatter'];
    }

    public function load(Rows $rows) : void
    {
        try {
            $stream = \fopen($this->url, $this->mode->value);
        } catch (\Throwable $e) {
            throw new RuntimeException("Can't open stream for url: {$this->url} in mode: {$this->mode->value}. Reason: " . $e->getMessage(), (int) $e->getCode(), $e);
        }

        if ($stream === false) {
            throw new RuntimeException("Can't open stream for url: {$this->url} in mode: {$this->mode->value}");
        }

        switch ($this->output) {
            case Output::rows:
                $output = $this->formatter->format($rows, $this->truncate);
                break;
            case Output::schema:
                $output = $this->schemaFormatter->format($rows->schema());
                break;
            case Output::rows_and_schema:
                $output = $this->formatter->format($rows, $this->truncate) . "\n" . $this->schemaFormatter->format($rows->schema());
                break;
        }

        \fwrite($stream, $output);

        \fclose($stream);
    }
}
