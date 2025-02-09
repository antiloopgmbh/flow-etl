<?php

declare(strict_types=1);

namespace Flow\ETL\Loader;

use Flow\ETL\Loader;
use Flow\ETL\Pipeline\Closure;
use Flow\ETL\Rows;

/**
 * @implements Loader<array{overflow_loader: Loader, buffer_size: int}>
 */
final class BufferLoader implements Closure, Loader
{
    private Rows $buffer;

    public function __construct(private readonly Loader $overflowLoader, private readonly int $bufferSize)
    {
        $this->buffer = new Rows();
    }

    public function __serialize() : array
    {
        return [
            'overflow_loader' => $this->overflowLoader,
            'buffer_size' => $this->bufferSize,
        ];
    }

    public function __unserialize(array $data) : void
    {
        $this->buffer = new Rows();
        $this->overflowLoader = $data['overflow_loader'];
        $this->bufferSize = $data['buffer_size'];
    }

    public function closure(Rows $rows) : void
    {
        if ($this->buffer->count()) {
            $this->overflowLoader->load($rows);
        }
    }

    public function load(Rows $rows) : void
    {
        if ($this->buffer->count() < $this->bufferSize) {
            $this->buffer = $this->buffer->merge($rows);
        }

        if ($this->buffer->count() > $this->bufferSize) {
            foreach ($this->buffer->chunks($this->bufferSize) as $bufferChunk) {
                if ($bufferChunk->count() === $this->bufferSize) {
                    $this->overflowLoader->load($bufferChunk);
                } else {
                    $this->buffer = $bufferChunk;
                }
            }
        }

        if ($this->buffer->count() === $this->bufferSize) {
            $this->overflowLoader->load($this->buffer);

            $this->buffer = new Rows();
        }
    }
}
