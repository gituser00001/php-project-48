<?php

namespace Differ\Differ;

function genDiff($pathToFile1, $pathToFile2, $format = 'empty')
{
    $contentFromFile1 = getContent($pathToFile1);
    $contentFromFile2 = getContent($pathToFile2);

    $dataFromFile1 = json_decode($contentFromFile1, true);
    $dataFromFile2 = json_decode($contentFromFile2, true);

    $result = buildTree($dataFromFile1, $dataFromFile2);
    return getFormattedDifference($result);
}

function buildTree($dataAfter, $dataBefore)
{
    $keys = array_unique(array_merge(array_keys($dataAfter), array_keys($dataBefore)));
    sort($keys);

    $tree = array_map(function ($key) use ($dataAfter, $dataBefore) {

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

    return $tree;
}

function isBool($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } else {
        return $value;
    }
}

function getFormattedDifference($builderTree)
{
    $result = array_map(function ($node) {
        $type = $node['type'];
        $key = $node['key'];
        $value = isBool($node['value']);

        switch ($type) {
            case 'deleted':
                return "- $key: $value" . PHP_EOL;
            case 'added':
                return "+ $key: $value" . PHP_EOL;
            case 'unchanged':
                return "  $key: $value" . PHP_EOL;
            case 'changed':
                $newValue = isBool($node['NewValue']);
                return "- $key: $value" . PHP_EOL . "+ $key: $newValue" . PHP_EOL;
        }
    }, $builderTree);

    return "{\n" . implode('', $result) . "}\n";
}

function getContent($pathToFile)
{
    $realPath = realpath($pathToFile);

    if (!$realPath) {
        throw new \Exception("wrong file path {$pathToFile}");
    }

    return file_get_contents($realPath);
}
