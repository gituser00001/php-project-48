<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseData(string $file, string $extension): array
{
    switch ($extension) {
        case 'json':
            return json_decode($file, true);
        case 'yaml' || 'yml':
            return Yaml::parse($file);
        default:
            return throw new \Exception("Unknow file type {$extension}");
    }
}