<?php

namespace Differ\Differ;

use function Differ\Parsers\parseData;
use function Differ\Formatter\getFormattedDifference;

function genDiff($pathToFile1, $pathToFile2, $format = 'stylish')
{
    [$content1, $extension1] = getContent($pathToFile1);
    [$content2, $extension2] = getContent($pathToFile2);

    $dataFromFile1 = parseData($content1, $extension1);
    $dataFromFile2 = parseData($content2, $extension2);

    $result = buildTree($dataFromFile1, $dataFromFile2);
    return getFormattedDifference($result, $format);
}

function buildTree($dataAfter, $dataBefore)
{
    $keys = array_unique(array_merge(array_keys($dataAfter), array_keys($dataBefore)));
    sort($keys);

    return array_map(function ($key) use ($dataAfter, $dataBefore) {

        if (!array_key_exists($key, $dataBefore)) {
            return ['key' => $key, 'value' => $dataAfter[$key], 'type' => 'deleted'];
        }

        if (!array_key_exists($key, $dataAfter)) {
            return ['key' => $key, 'value' => $dataBefore[$key], 'type' => 'added'];
        }

        if ($dataAfter[$key] === $dataBefore[$key]) {
            return ['key' => $key, 'value' => $dataBefore[$key], 'type' => 'unchanged'];
        }

        return ['key' => $key, 'value' => $dataBefore[$key], 'NewValue' => $dataAfter[$key], 'type' => 'changed'];
    }, $keys);
}

function isBool($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } else {
        return $value;
    }
}

function getContent($pathToFile)
{
    $realPath = realpath($pathToFile);

    if (!$realPath) {
        throw new \Exception("wrong file path {$pathToFile}");
    }

    $content = file_get_contents($realPath);
    $extension = pathinfo($content, PATHINFO_EXTENSION);

    return [$content, $extension];
}
