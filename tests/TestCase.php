<?php

namespace Tests;


use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
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