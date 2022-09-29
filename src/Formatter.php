<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Formatters\Plain\format as formatPlain;

function getFormattedDifference(array $diff, string $formatName)
{
    switch ($formatName) {
        case 'stylish':
            return formatStylish($diff);
        case 'plain':
            return formatPlain($diff);
        default:
            throw new \Exception("Unknown format {$formatName}");
    }
}
