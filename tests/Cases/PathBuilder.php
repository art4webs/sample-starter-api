<?php

namespace App\Tests\Cases;

final class PathBuilder
{
    public static function build(...$segments): string
    {
        return implode(DIRECTORY_SEPARATOR, $segments);
    }
}
