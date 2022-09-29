<?php

namespace Differ\Formatters\Stylish;

function format(array $data): string
{
    $strings = getStylish($data);
    $result = implode("\n", $strings);

    return "{\n{$result}\n}";
}


function getStylish(array $diffTree, int $depth = 0): array
{
    $indent = getIndent($depth);
    $nextDepth = $depth + 1;
    $result = array_map(function (array $node) use ($indent, $nextDepth) {
        $key = $node['key'];
        $type = $node['type'];
        switch ($type) {
            case 'deleted':
                $value = makeString($node['value'], $nextDepth);
                return "{$indent}  - {$key}: {$value}";

            case 'added':
                $value = makeString($node['value'], $nextDepth);
                return "{$indent}  + {$key}: {$value}";

            case 'unchanged':
                $value = makeString($node['value'], $nextDepth);
                return "{$indent}    {$key}: {$value}";

            case 'changed':
                $oldValue = makeString($node['oldValue'], $nextDepth);
                $newValue = makeString($node['newValue'], $nextDepth);
                return "{$indent}  - {$key}: {$oldValue}\n{$indent}  + {$key}: {$newValue}";

            case 'nested':
                $child = getStylish($node['children'], $nextDepth);
                $stringNested = implode("\n", $child);
                return "{$indent}    {$key}: {\n{$stringNested}\n{$indent}    }";

            default:
                throw new \Exception("Unknown type {$type}");
        }
    }, $diffTree);

    return $result;
}


function makeString(mixed $value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        $result = arrayToString($value, $depth);
        $indent = getIndent($depth);
        $modified = "{{$result}\n{$indent}}";

        return $modified;
    }

    return "{$value}";
}


function arrayToString(array $arrayValue, int $depth): string
{
    $keys = array_keys($arrayValue);
    $inDepth = $depth + 1;
    $result = array_map(function ($key) use ($arrayValue, $inDepth) {
        $val = makeString($arrayValue[$key], $inDepth);
        $indent = getIndent($inDepth);
        $result = "\n{$indent}{$key}: {$val}";

        return $result;
    }, $keys);

    return implode('', $result);
}

function getIndent(int $repeat): string
{
    return str_repeat('    ', $repeat);
}
