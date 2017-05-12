<?php

namespace Tests;


class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function loadFixture($filename)
    {
        $path = __DIR__ . '/fixtures/' . $filename;
        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return null;
    }
}