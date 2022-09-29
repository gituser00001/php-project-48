<?php

namespace Differ\Formatters\Plain;

function format(array $data): string
{
    $lines = makePlain($data);
    return implode("\n", $lines);
}

function makePlain(array $diffTree, string $path = ''): array
{
    $result = array_map(function ($node) use ($path) {
        $key = $node['key'];
        $type = $node['type'];
        $property = "{$path}{$key}";
        switch ($type) {
            case 'deleted':
                return "Property '{$property}' was removed";

            case 'added':
                $value = makeString($node['value']);
                return "Property '{$property}' was added with value: {$value}";

            case 'unchanged':
                break;

            case 'changed':
                $oldValue = makeString($node['oldValue']);
                $newValue = makeString($node['newValue']);
                return "Property '$property' was updated. From {$oldValue} to {$newValue}";

            case 'nested':
                $nestedPath = "{$path}{$key}.";
                $nestedNode = implode("\n", makePlain($node['children'], $nestedPath));
                return $nestedNode;

            default:
                throw new \Exception("Unknown type {$type}");
        }
    }, $diffTree);

    return array_filter($result);
}

function makeString($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        return '[complex value]';
    }

    if (is_numeric($value)) {
        return "{$value}";
    }

    return "'{$value}'";
}
