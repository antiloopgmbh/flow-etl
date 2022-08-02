<?php

declare(strict_types=1);

namespace Flow\ArrayComparison;

use Symfony\PolyFill\Php81\Php81;

/**
 * @psalm-immutable
 */
final class ArraySortByKey
{
    /**
     * @param array<mixed> $array
     *
     * @return array<mixed>
     */
    public function __invoke(array $array) : array
    {
        $array = \array_map(
            fn ($value) => \is_array($value) ? (new self)($value) : $value,
            $array
        );

        if (Php81::array_is_list($array)) {
            \usort($array, fn ($a, $b) : int => $a <=> $b);
        } else {
            \uksort($array, fn ($a, $b) : int => $a <=> $b);
        }

        return $array;
    }
}
