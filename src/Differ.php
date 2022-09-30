<?php

namespace Differ\Differ;

use function Differ\Parsers\parseData;
use function Differ\Formatter\getFormattedDifference;
use function Functional\sort;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish')
{
    [$content1, $extension1] = getContent($pathToFile1);
    [$content2, $extension2] = getContent($pathToFile2);

    $dataFromFile1 = parseData($content1, $extension1);
    $dataFromFile2 = parseData($content2, $extension2);

    $result = buildTree($dataFromFile1, $dataFromFile2);
    return getFormattedDifference($result, $format);
}

function buildTree(array $dataBefore, array $dataAfter)
{
    $keys = array_unique(array_merge(array_keys($dataBefore), array_keys($dataAfter)));
    $keysSorted = sort($keys, fn ($left, $right) => $left <=> $right);

    $tree = array_map(function ($key) use ($dataBefore, $dataAfter) {

        $valueAfter = $dataAfter[$key] ?? null;
        $valueBefor = $dataBefore[$key] ?? null;

        if (!array_key_exists($key, $dataAfter)) {
            return [
                'key' => $key,
                'value' => $valueBefor,
                'type' => 'deleted'];
        }

        if (!array_key_exists($key, $dataBefore)) {
            return [
                'key' => $key,
                'value' => $valueAfter,
                'type' => 'added'];
        }

        if ($valueAfter === $valueBefor) {
            return [
                'key' => $key,
                'value' => $valueBefor,
                'type' => 'unchanged'];
        }

        if (is_array($valueBefor) && is_array($valueAfter)) {
            return [
                'key' => $key,
                'type' => 'nested',
                'children' => buildTree($valueBefor, $valueAfter)
            ];
        }

        return [
            'key' => $key,
            'oldValue' => $valueBefor,
            'newValue' => $valueAfter,
            'type' => 'changed'];
    }, $keysSorted);

    return $tree;
}


function getContent(string $pathToFile)
{
    $realPath = realpath($pathToFile);

    if (!$realPath) {
        throw new \Exception("wrong file path {$pathToFile}");
    }

    $content = file_get_contents($realPath);
    $extension = pathinfo($realPath, PATHINFO_EXTENSION);
    return [$content, $extension];
}
