<?php

namespace Differ\Formatters\Json;

function format(array $data)
{
    return json_encode($data);
}
