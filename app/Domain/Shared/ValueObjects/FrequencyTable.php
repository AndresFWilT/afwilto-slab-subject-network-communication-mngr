<?php

namespace App\Domain\Shared\ValueObjects;

use App\Domain\Shared\Errors\EmptyInputException;
use App\Domain\Shared\Errors\SingleCharacterException;

final class FrequencyTable
{
    /** @var array<string, int> symbol → count, sorted descending by count */
    private readonly array $table;

    private function __construct(array $table)
    {
        $this->table = $table;
    }

    public static function fromText(string $text): self
    {
        if ($text === '') {
            throw new EmptyInputException('Input text cannot be empty.');
        }

        $table = [];
        $length = mb_strlen($text);
        for ($i = 0; $i < $length; $i++) {
            $ch = mb_substr($text, $i, 1);
            $table[$ch] = ($table[$ch] ?? 0) + 1;
        }

        if (count($table) === 1) {
            throw new SingleCharacterException('Input must contain at least 2 distinct characters.');
        }

        arsort($table);
        return new self($table);
    }

    /** @return array<string, int> */
    public function entries(): array
    {
        return $this->table;
    }

    public function totalCount(): int
    {
        return array_sum($this->table);
    }

    public function symbolCount(): int
    {
        return count($this->table);
    }
}
