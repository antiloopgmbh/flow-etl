<?php

declare(strict_types=1);

namespace Flow\ETL\Pipeline;

use Flow\ETL\Loader;
use Flow\ETL\Transformer;
use Flow\Serializer\Serializable;

/**
 * @implements Serializable<array{pipes: array<int, Loader|Transformer>}>
 */
final class Pipes implements Serializable
{
    /**
     * @var Loader[]|Transformer[]
     */
    private array $pipes;

    /**
     * @param array<int, Loader|Transformer> $pipes
     */
    public function __construct(array $pipes)
    {
        $this->pipes = $pipes;
    }

    public static function empty() : self
    {
        return new self([]);
    }

    public function __serialize() : array
    {
        return [
            'pipes' => $this->pipes,
        ];
    }

    public function __unserialize(array $data) : void
    {
        $this->pipes = $data['pipes'];
    }

    public function add($pipe) : void
    {
        $this->pipes[] = $pipe;
    }

    /**
     * @return array<Loader|Transformer>
     */
    public function all() : array
    {
        return $this->pipes;
    }
}
