<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\format;

function getFormattedDifference(array $diff, string $formatName)
{
    switch ($formatName) {
        case 'stylish':
            return format($diff);
        default:
            throw new \Exception("Unknown format {$formatName}");
    }
}
