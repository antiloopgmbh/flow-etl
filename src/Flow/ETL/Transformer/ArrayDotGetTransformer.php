<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use function Flow\ArrayDot\array_dot_get;
use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Row\EntryFactory;
use Flow\ETL\Row\Factory\NativeEntryFactory;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @implements Transformer<array{array_entry_name: string, path: string, new_entry_name: string, entry_factory: EntryFactory}>
 * @psalm-immutable
 */
final class ArrayDotGetTransformer implements Transformer
{
    private string $arrayEntryName;
    private string $path;
    private string $newEntryName;
    private ?EntryFactory $entryFactory;

    public function __construct(
        string $arrayEntryName,
        string $path,
        string $newEntryName = 'element',
        EntryFactory $entryFactory = null
    ) {
        if ($entryFactory === null) {
            $entryFactory = new NativeEntryFactory();
        }

        $this->arrayEntryName = $arrayEntryName;
        $this->path = $path;
        $this->newEntryName = $newEntryName;
        $this->entryFactory = $entryFactory;
    }

    public function __serialize() : array
    {
        return [
            'array_entry_name' => $this->arrayEntryName,
            'path' => $this->path,
            'new_entry_name' => $this->newEntryName,
            'entry_factory' => $this->entryFactory,
        ];
    }

    public function __unserialize(array $data) : void
    {
        $this->arrayEntryName = $data['array_entry_name'];
        $this->path = $data['path'];
        $this->newEntryName = $data['new_entry_name'];
        $this->entryFactory = $data['entry_factory'];
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            $arrayEntry = $row->get($this->arrayEntryName);

            if (!$arrayEntry instanceof Row\Entry\ArrayEntry) {
                $entryClass = get_class($arrayEntry);

                throw new RuntimeException("{$this->arrayEntryName} is not ArrayEntry but {$entryClass}");
            }

            return $row->set(
                $this->entryFactory->create(
                    $this->newEntryName,
                    array_dot_get($arrayEntry->value(), $this->path)
                )
            );
        };

        return $rows->map($transformer);
    }
}
