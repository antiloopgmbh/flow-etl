<?php

declare(strict_types=1);

namespace Flow\ETL\CLI;

final class Input
{
    private array $argv;

    /**
     * @param array<string> $argv
     */
    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    /**
     * @return array<string>
     */
    public function argv() : array
    {
        return $this->argv;
    }

    public function optionValue(string $name, string $default = null) : ?string
    {
        foreach ($this->argv as $arg) {
            $parts = \explode('=', $arg);

            if (\count($parts) !== 2) {
                continue;
            }

            if ($parts[0] === '--' . \strtolower($name)) {
                return $parts[1];
            }
        }

        return $default;
    }
}
